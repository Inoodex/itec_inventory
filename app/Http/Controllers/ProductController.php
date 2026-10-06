<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Service;
use App\Models\Admin\Brand;
use App\Models\Admin\Category;
use App\Models\Admin\SubCategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
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
            return redirect()->back()->with(['error' => 'Product not found. Please try again.'])->withInput();
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
}