<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private InventoryService $inventoryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inventoryService = new InventoryService();
    }

    public function test_increment_stock_creates_inventory_if_not_exists(): void
    {
        $brand = Brand::create(['name' => 'Test Brand', 'status' => '1']);
        $category = Category::create(['name' => 'Test Cat', 'status' => '1']);
        $product = Product::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Test Item',
            'model' => 'T-100',
            'status' => 1,
        ]);

        $this->inventoryService->incrementStock($product->id, 10);

        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'current_stock' => 10,
        ]);
    }

    public function test_decrement_stock_throws_exception_when_insufficient(): void
    {
        $brand = Brand::create(['name' => 'Test Brand', 'status' => '1']);
        $category = Category::create(['name' => 'Test Cat', 'status' => '1']);
        $product = Product::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Test Item',
            'model' => 'T-100',
            'status' => 1,
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'current_stock' => 3,
            'opening_stock' => 3,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->inventoryService->decrementStock($product->id, 5);
    }

    public function test_restore_stock_increments_available_quantity(): void
    {
        $brand = Brand::create(['name' => 'Test Brand', 'status' => '1']);
        $category = Category::create(['name' => 'Test Cat', 'status' => '1']);
        $product = Product::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Test Item',
            'model' => 'T-100',
            'status' => 1,
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'current_stock' => 5,
            'opening_stock' => 5,
        ]);

        $this->inventoryService->restoreStock($product->id, 2);

        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'current_stock' => 7,
        ]);
    }
}
