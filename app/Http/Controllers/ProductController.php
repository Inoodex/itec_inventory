<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Service;

use App\Models\Admin\Size;
use App\Models\Admin\Brand;
use Illuminate\Support\Str;
use App\Models\Admin\Toping;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Admin\Category;
use App\Models\Admin\ProductTag;
use App\Models\Admin\OptionTitle;
use App\Models\Admin\ProductSize;
use App\Models\Admin\SubCategory;
use App\Models\SizeVsTopingPrice;
use App\Models\Admin\ProductImage;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\ProductOption;
use App\Models\Admin\ProductToping;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Models\Admin\ProductOptionTopping;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category', 'inventory', 'latestPurchase', 'availableSerials']);

        // Filter by search term
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->get();
        $brands = Brand::where('status', '1')->latest()->get();
        $categories = Category::where('status', '1')->latest()->get();
        
        return view('frontend.pages.product.index', compact('products', 'brands', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('for_book_or_product', '2')->where('status', '1')->get();
        $subCategories = SubCategory::where('for_book_or_product', '2')->where('status', '1')->get();
        $tmp = [];
        foreach($subCategories as $subCategory){
            $tmp[$subCategory->category_id][] = $subCategory;
        }
        $subCategories = $tmp;
        $brands = Brand::where('status', '1')->get();

        return view('admin.pages.product.create', compact('categories','subCategories','brands'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('products', 'public');
                $photoPaths[] = $path;
            }
        }

        $barcode = !empty($validated['barcode']) ? trim($validated['barcode']) : Product::generateBarcode();

        $product = Product::create([
            'brand_id'      => $validated['brand_id'],
            'category_id'   => $validated['category_id'] ?? null,
            'name'          => $validated['name'],
            'model'         => $validated['model_name'],
            'barcode'       => $barcode,
            'warranty'      => $validated['warranty'] ?? 0,
            'status'        => $validated['status'],
            'is_serialized' => $request->has('is_serialized') ? 1 : 0,
            'photos'        => !empty($photoPaths) ? $photoPaths : null,
        ]);

        return redirect()->route('products.index')->with('success', 'Product created successfully with Barcode: ' . $product->barcode);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::where('id', $id)->first();
        $categories = Category::where('for_book_or_product', '2')->where('status', '1')->get();
        $subCategories = SubCategory::where('for_book_or_product', '2')->where('status', '1')->get();
        $tmp = [];
        foreach($subCategories as $subCategory){
            $tmp[$subCategory->category_id][] = $subCategory;
        }
        $subCategories = $tmp;

        $brands = Brand::where('status','1')->get();

        if(!$product){
            return redirect()->back()->with(['error' => getNotify(10)])->withInput();
        }

        return view('admin.pages.product.edit', compact('categories', 'product', 'id', 'subCategories','brands'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        // Get remaining photos from hidden input
        $remainingPhotos = [];
        if ($request->remaining_photos) {
            $remainingPhotos = json_decode($request->remaining_photos, true) ?? [];
        }

        // Find deleted photos and remove them from storage
        $originalPhotos = $product->photos ?? [];
        $deletedPhotos = array_diff($originalPhotos, $remainingPhotos);
        
        foreach ($deletedPhotos as $deletedPhoto) {
            if (Storage::disk('public')->exists($deletedPhoto)) {
                Storage::disk('public')->delete($deletedPhoto);
            }
        }

        // Handle new photo uploads
        $newPhotoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('products', 'public');
                $newPhotoPaths[] = $path;
            }
        }

        // Merge remaining and new photos
        $allPhotos = array_merge($remainingPhotos, $newPhotoPaths);

        $barcode = !empty($validated['barcode']) ? trim($validated['barcode']) : ($product->barcode ?? Product::generateBarcode());

        $product->update([
            'brand_id'      => $validated['brand_id'],
            'category_id'   => $validated['category_id'] ?? $product->category_id,
            'name'          => $validated['name'],
            'model'         => $validated['model_name'],
            'barcode'       => $barcode,
            'warranty'      => $validated['warranty'] ?? 0,
            'status'        => $validated['status'],
            'is_serialized' => $request->has('is_serialized') ? 1 : 0,
            'photos'        => !empty($allPhotos) ? $allPhotos : null,
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Universal Barcode / Serial Scanner Lookup API Endpoint
     */
    public function barcodeLookup(Request $request)
    {
        $code = trim($request->query('code', ''));

        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a barcode or serial number.'
            ], 400);
        }

        // 1. Check if it matches a Product Serial Number
        $serial = \App\Models\ProductSerial::with(['product.brand', 'product.category', 'product.inventory', 'product.latestPurchase', 'salesItem.sale.customer'])
            ->where('serial_number', $code)
            ->first();

        if ($serial) {
            $product = $serial->product;
            return response()->json([
                'success' => true,
                'type' => 'serial',
                'status' => $serial->status, // available, sold, damaged, returned
                'serial_number' => $serial->serial_number,
                'product' => [
                    'id'             => $product->id,
                    'name'           => $product->name,
                    'model'          => $product->model,
                    'brand'          => $product->brand->name ?? '',
                    'category'       => $product->category->name ?? '',
                    'barcode'        => $product->barcode,
                    'warranty_days'  => $product->warranty ?? 0,
                    'stock'          => $product->inventory->current_stock ?? 0,
                    'purchase_price' => $product->latestPurchase->unit_price ?? 0,
                    'selling_price'  => $product->latestPurchase->unit_price ?? 0,
                    'is_serialized'  => 1,
                ],
                'sale' => $serial->salesItem ? [
                    'invoice_no'    => $serial->salesItem->sale->order_no ?? '',
                    'sale_date'     => $serial->salesItem->sale->created_at?->format('Y-m-d') ?? '',
                    'customer_name' => $serial->salesItem->sale->customer->name ?? '',
                    'customer_phone'=> $serial->salesItem->sale->customer->phone ?? '',
                ] : null
            ]);
        }

        // 2. Check if it matches a Product Vendor Barcode or Model
        $product = Product::with(['brand', 'category', 'inventory', 'latestPurchase', 'availableSerials'])
            ->where('barcode', $code)
            ->orWhere('model', $code)
            ->first();

        if ($product) {
            return response()->json([
                'success' => true,
                'type' => 'product',
                'status' => 'available',
                'product' => [
                    'id'                => $product->id,
                    'name'              => $product->name,
                    'model'             => $product->model,
                    'brand'             => $product->brand->name ?? '',
                    'category'          => $product->category->name ?? '',
                    'barcode'           => $product->barcode,
                    'warranty_days'     => $product->warranty ?? 0,
                    'stock'             => $product->inventory->current_stock ?? 0,
                    'purchase_price'    => $product->latestPurchase->unit_price ?? 0,
                    'selling_price'     => $product->latestPurchase->unit_price ?? 0,
                    'is_serialized'     => $product->is_serialized ? 1 : 0,
                    'available_serials' => $product->availableSerials->pluck('serial_number'),
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "No product or serial found matching [{$code}]."
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::where('id', $id)->first();
        if(!$product) abort(404);

        $product->delete();
        return redirect()->back()->with('success', 'Product delete successfully');
    }

    public function size($id)
    {
        $productSizes = ProductSize::join('sizes', 'sizes.id', '=', 'product_sizes.size_id')
            ->where('product_id', $id)
            ->select('product_sizes.*', 'sizes.name')->get();
        return view('admin.pages.product.product_size', compact('id', 'productSizes'));
    }

    public function getProductSize(Request $request){

        $productSizes = ProductSize::join('sizes', 'sizes.id', '=', 'product_sizes.size_id')
            ->where('product_sizes.product_id', $request->id)
            ->where('product_sizes.status', '1')
            ->select('product_sizes.*', 'sizes.name')->get();
        $product = Product::where('id', $request->id)->first();

        return ['product' => $product, 'productSizes' => $productSizes];
    }

    public function createProductSize($id)
    {
        $product = Product::where('id', $id)->first();
        if(!$product){
            return redirect()->back()->with(['error' => getNotify(10)])->withInput();
        }
        $sizes = Size::where('status', '1')->get();
        return view('admin.pages.product.create_product_size', compact('id', 'sizes','product'));
    }

    public function editProductSize($id)
    {
        $productSize = ProductSize::find($id);
        if(!$productSize){
            return redirect()->back()->with(['error' => getNotify(10)]);
        }
        $product = Product::where('id', $productSize->product_id)->first();
        if(!$product){
            return redirect()->back()->with(['error' => getNotify(10)]);
        }

        $sizes = Size::where('status', '1')->get();
        if ($productSize) {
            return view('admin.pages.product.edit_product_size', compact('productSize', 'sizes', 'product'));
        }
    }

    public function storeSize(Request $request)
    {
        $request->validate([
            'product_id' => 'required|numeric',
            'size_id' => 'required|numeric',
            'price' => 'nullable|numeric',
            'status' => 'required|in:0,1',
            // 'description' => 'required',
            'offer_price' => 'nullable|numeric',
            'offer_from' => 'nullable|date',
            'offer_to' => 'nullable|date',
            'quantity' => 'numeric|nullable'
        ]);

        $product = Product::where('id', $request->product_id)->first();
        if(!$product){
            return redirect()->back()->with(['error' => getNotify(10)]);
        }

        if($product->is_size_wise_price == '1' && $request->price==""){
            return redirect()->back()->with(['error' => 'Price field is required.', 'error_code' => 'edit'])->withInput();
        }

        $imageName = "";
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $destinationPath = public_path('frontend/product_images/');
            $imageName = now()->format('YmdHis') . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $imageName);
        }

        $size = new ProductSize;
        $size->product_id = $request->product_id;
        $size->size_id = $request->size_id;
        $size->price = $request->price??0;
        $size->offer_price = $request->offer_price;
        $size->offer_from = $request->offer_from;
        $size->offer_to = $request->offer_to;
        $size->quantity = $request->quantity;
        $size->description = $request->description;
        $size->status = $request->status;
        $size->created_by = auth()->user()->id;
        $size->image = $imageName;
        $size->save();

        return redirect()->back()->with(['success' => getNotify(1)]);
    }
    //Assign topings
    public function topings($id)
    {
        $productTopings = ProductToping::join('topings', 'topings.id', '=', 'product_topings.toping_id')->where('product_topings.product_id', $id)->select('topings.*', 'product_topings.id as topId')->get();
        $topings = Toping::where('status', '1')->get();
        return view('admin.pages.product.topings', compact('productTopings', 'topings', 'id'));
    }

    public function storeToping(Request $request)
    {
        $request->validate([
            'product_id' => 'required|numeric',
            'toping' => 'required|numeric',
            'status' => 'required|in:0,1',
        ]);

        $checkExist = ProductToping::where('product_id', $request->product_id)->where('toping_id', $request->toping)->first();
        if (!$checkExist) {
            $size = new ProductToping();
            $size->product_id = $request->product_id;
            $size->toping_id = $request->toping;
            $size->status = $request->status;
            $size->created_by = auth()->user()->id;
            $size->save();
            session()->flash('sweet_alert', [
                'type' => 'success',
                'title' => 'Success!',
                'text' => 'Product toping added success',
            ]);
        } else {
            session()->flash('sweet_alert', [
                'type' => 'warning',
                'title' => 'warning!',
                'text' => 'Already exists this toping! Try another',
            ]);
        }


        return redirect()->back();
    }

    public function updateSize(Request $request, $id)
    {
        // return $request->all();
        $request->validate([
            'product_id' => 'required|numeric',
            'size_id' => 'required|numeric',
            'price' => 'nullable|numeric',
            'status' => 'required|in:0,1',
            'offer_price' => 'nullable|numeric',
            'offer_from' => 'nullable|date',
            'offer_to' => 'nullable|date',
            'quantity' => 'numeric|nullable'
        ]);

        $product = Product::where('id', $request->product_id)->first();
        if(!$product){
            return redirect()->back()->with(['error' => getNotify(10)]);
        }

        if($product->is_size_wise_price != '1' && $request->price==""){
            return redirect()->back()->with(['error' => 'Price field is required.', 'error_code' => 'edit'])->withInput();
        }
        
        $size = ProductSize::find($id);
        if ($size) {

            $imageName = $size->image;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $destinationPath = public_path('frontend/product_images/');
                $imageName = now()->format('YmdHis') . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $imageName);
                if ($size->image)
                    unlink(public_path('frontend/product_images/' . $size->image));
            }


            $size->size_id = $request->size_id;
            $size->price = $request->price??0;
            $size->offer_price = $request->offer_price;
            $size->offer_from = $request->offer_from;
            $size->offer_to = $request->offer_to;
            $size->quantity = $request->quantity;
            $size->status = $request->status;
            $size->description = $request->description;
            $size->image = $imageName;
            $size->updated_by = auth()->user()->id;
            $size->update();

            return redirect()->back()->with(['success' => getNotify(2)]);
        }
    }

    public function deleteProductSize($id)
    {
        $productSizes = ProductSize::find($id);
        if ($productSizes)
            $productSizes->delete();
        session()->flash('sweet_alert', [
            'type' => 'success',
            'title' => 'Success!',
            'text' => 'Product Size delete success',
        ]);
        return redirect()->back();
    }

    public function getProducts()
    {
        $categories = Category::leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->select(
                'categories.id as category_id',
                'categories.order_by as OrderBY',
                'categories.name as category_name',
                'products.id as product_id',
                'products.name as product_name',
                'products.description as description',
                'products.image as image',
            )
            ->where('products.status', '1')
            ->orderBy('categories.order_by')
            ->orderBy('products.id')
            ->get();

            $currentDate = Carbon::today();
            foreach($categories as $key => $category){
                $productSizes = ProductSize::where('product_id',$category->product_id)->get();
                $offerMin = null;
                $regularMin = null;
                foreach($productSizes as $size){
                    if ($size->offer_from <= $currentDate && $currentDate <= $size->offer_to) {
                        $offerPrice = $size->offer_price;
                        if($offerMin==null)$offerMin = $offerPrice;
                        $$offerMin =  min($offerMin,$offerPrice);
                    }
                    $price = $size->price;
                    if($regularMin==null) $regularMin = $price;
                    $regularMin = min($regularMin, $price);
                }
                $categories[$key]->calculated_offer_price = ($offerMin<$regularMin ? $offerMin : null);
                $categories[$key]->min_price = $regularMin;
            }


        // return $categories;
        // Organize the result into a more usable format
        $groupedCategories = [];
        $categories = $categories->sortBy('order_by');
        foreach ($categories as $category) {
            // $category->min_price = null;
            // $category->calculated_offer_price = null;
            $categoryId = $category->category_id;
            if (!isset($groupedCategories[$categoryId])) {
                $groupedCategories[$categoryId] = [
                    'category_id' => $category->category_id,
                    'category_name' => $category->category_name,
                    'order_by' => $category->OrderBY,
                    'products' => [],
                ];
            }
            if ($category->product_id) {
                $groupedCategories[$categoryId]['products'][] = [
                    'id' => $category->product_id,
                    'name' => $category->product_name,
                    'description' => $category->description,
                    'image' => $category->image,
                    'min_price' => $category->min_price,
                    'calculated_offer_price' => $category->calculated_offer_price,
                ];
            }
        }
        $productAllTages = ProductTag::pluck('tag_name', 'id');
        return [$groupedCategories, $productAllTages];
    }

    public function getProductDetails(Request $request)
    {
        $productId = $request->query('id');
        $product = Product::where('id', $productId)->first();
        $productSizes = ProductSize::join('sizes', 'sizes.id', '=', 'product_sizes.size_id')
            ->where('product_id', $productId)
            ->where('product_sizes.status', '1')
            ->select('product_sizes.*', 'sizes.name', 'sizes.id as size_id')
            ->get();


        $currentDate = Carbon::today();
        $maxPrice = $productSizes->max('price');
        $minPrice = $productSizes->min('price');
        $tem = [];

        foreach ($productSizes as $row) {
            if ($row->offer_from <= $currentDate && $currentDate <= $row->offer_to) {
                $row->price = $row->offer_price;
            }
            $tem[$row->id] = $row;
        }
        $productSizes = $tem;
        $productTopings = ProductToping::join('topings', 'topings.id', '=', 'product_topings.toping_id')
            ->where('product_topings.product_id', $productId)
            ->where('product_topings.status', '1')
            ->select('topings.*')
            ->get();
        $favoritToppingsIds = [];
        foreach ($productTopings as $toping) {
            $favoritToppingsIds[$toping->id] = $toping->id;
        }


        $tem = [];
        foreach ($productTopings as $row) {
            $tem[$row->id] = $row;
        }
        $productTopings = $tem;

        $allTopings = Toping::where('status', '1')->get();

        $tem = [];
        foreach ($allTopings as $row) {
            $tem[$row->id] = $row;
        }
        $allTopings = $tem;

        $moreTopings = Toping::whereNotIn('id', $favoritToppingsIds)->where('status', '1')->get();

        $tem = [];
        foreach ($moreTopings as $row) {
            $tem[$row->id] = $row;
        }
        $moreTopings = $tem;

        $sizeVsTopings = SizeVsTopingPrice::get();
        $bindData = [];
        foreach ($sizeVsTopings as $item) {
            $bindData[$item->toping_id][$item->size_id] = $item->price;
        }
        $sizeVsTopings = $bindData;

        $maxMin = [$minPrice, $maxPrice];

        $productTages = ProductTag::where('pro_id', $productId)->get()->toArray();

        $options = ProductOption::join('product_option_toppings as option_topping', 'option_topping.product_option_id', '=', 'product_options.id')
            ->join('option_titles', 'option_titles.id', '=', 'product_options.title_id')
            ->where('product_options.product_id', $productId)
            ->select('option_topping.*', 'product_options.title_id', 'product_options.type', 'product_options.free_qty', 'option_titles.name')->get();

        $temp = [];
        foreach ($options as $option) {
            $option->type = strtolower($option->type);
            $temp[$option->product_option_id]['details']['title'] = $option->name;
            $temp[$option->product_option_id]['details']['freeQty'] = $option->free_qty;
            $temp[$option->product_option_id]['options'][] = $option;
        }
        $productOptions = $temp;


        return response()->json([$product, $productSizes, $productTopings, $maxMin, $allTopings, $moreTopings, $sizeVsTopings, $productTages, $productOptions]);
    }


    public function getPopularProducts()
    {
        return $topSellingProducts = \DB::table('products')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('product_sizes', function ($join) {
                $join->on('products.id', '=', 'product_sizes.product_id')
                    ->whereRaw('NOW() BETWEEN product_sizes.offer_from AND product_sizes.offer_to');
            })
            ->select(
                'products.id',
                'products.name',
                'products.image',
                \DB::raw('COUNT(orders.id) as total_orders'),
                \DB::raw('(SELECT MIN(price) FROM product_sizes WHERE product_sizes.product_id = products.id) as min_price'),
                'product_sizes.offer_price as calculated_offer_price'
            )
            ->groupBy('products.id', 'products.name', 'products.image', 'product_sizes.offer_price')
            ->orderBy('total_orders', 'desc')
            ->limit(10)
            ->get();
    }

    public function getRelatedProduct(Request $request)
    {
        $product_ids = $request->product_ids;
        $product_ids = explode(",", $product_ids);
        $catIds = Product::whereIn("id", $product_ids)->pluck('category_id');
        $products = Product::whereIn('category_id', $catIds)->where('status', '1')->take(10)->get();

        $proData = [];
        foreach ($products as $pro) {
            $proData[] = [
                'id' => $pro->id,
                'name' => $pro->name,
                'image' => asset("frontend/product_images/$pro->image"),
            ];
        }

        return $proData;
    }
}