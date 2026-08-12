<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        // Filter by search term
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $categories = $query->orderBy('order_by')->paginate(10)->withQueryString();
        return view('frontend.pages.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('frontend.pages.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
            'order_by' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/categories'), $imageName);
            $data['image'] = 'uploads/categories/' . $imageName;
        }

        Category::create($data);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function show(Category $category)
    {
        $category->load('children', 'parent');
        return view('frontend.pages.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('frontend.pages.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
            'order_by' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/categories'), $imageName);
            $data['image'] = 'uploads/categories/' . $imageName;
        }

        $category->update($data);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        // Delete image if exists
        if ($category->image && file_exists(public_path($category->image))) {
            unlink(public_path($category->image));
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function getCategoriesByParent($parentId = null)
    {
        $categories = Category::where('parent_id', $parentId)
            ->where('status', 1)
            ->orderBy('order_by')
            ->get();

        return response()->json($categories);
    }
}
