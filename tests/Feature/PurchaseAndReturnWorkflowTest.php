<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Vendor;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseAndReturnWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private InventoryService $inventoryService;
    private Product $serializedProduct;
    private Product $nonSerializedProduct;

    protected function setUp(): void
    {
        parent::setUp();

        $this->inventoryService = new InventoryService();

        $brand = Brand::create(['name' => 'Dell', 'status' => '1']);
        $category = Category::create(['name' => 'Laptops', 'status' => '1', 'for_book_or_product' => '2']);

        $this->serializedProduct = Product::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Latitude 5420',
            'model' => 'LAT-5420',
            'barcode' => 'DELL-LAT-5420',
            'status' => '1',
            'is_serialized' => 1,
        ]);

        $this->nonSerializedProduct = Product::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'USB Cable',
            'model' => 'USB-C-1M',
            'barcode' => 'CABLE-USB-C',
            'status' => '1',
            'is_serialized' => 0,
        ]);
    }

    public function test_purchase_intake_increments_inventory_stock(): void
    {
        $this->inventoryService->incrementStock($this->nonSerializedProduct->id, 20);
        $this->assertEquals(20, $this->inventoryService->getStock($this->nonSerializedProduct->id));

        $this->inventoryService->incrementStock($this->nonSerializedProduct->id, 10);
        $this->assertEquals(30, $this->inventoryService->getStock($this->nonSerializedProduct->id));
    }

    public function test_serialized_purchase_creates_available_serials(): void
    {
        $vendor = Vendor::create([
            'name' => 'Dell Distributor',
            'email' => 'dell@example.com',
            'phone' => '123456789',
            'address' => 'Dhaka, Bangladesh',
        ]);
        $purchase = \App\Models\Purchase::create([
            'purchase_no' => 'PO-20261006-0001',
            'product_id' => $this->serializedProduct->id,
            'vendor_id' => $vendor->id,
            'quantity' => 3,
            'unit_price' => 50000,
            'total_price' => 150000,
            'payment' => 150000,
            'due' => 0,
        ]);

        $serials = ['SN-1001', 'SN-1002', 'SN-1003'];
        $this->inventoryService->registerSerials($this->serializedProduct->id, $purchase->id, $serials, 3);

        $this->assertDatabaseHas('product_serials', [
            'product_id' => $this->serializedProduct->id,
            'serial_number' => 'SN-1001',
            'status' => 'available',
        ]);
        $this->assertDatabaseHas('product_serials', [
            'product_id' => $this->serializedProduct->id,
            'serial_number' => 'SN-1002',
            'status' => 'available',
        ]);
        $this->assertDatabaseHas('product_serials', [
            'product_id' => $this->serializedProduct->id,
            'serial_number' => 'SN-1003',
            'status' => 'available',
        ]);
    }

    public function test_sale_decrement_and_return_stock_restoration_cycle(): void
    {
        $this->inventoryService->incrementStock($this->nonSerializedProduct->id, 15);
        $this->assertEquals(15, $this->inventoryService->getStock($this->nonSerializedProduct->id));

        // Deduct 5 for sale
        $this->inventoryService->decrementStock($this->nonSerializedProduct->id, 5);
        $this->assertEquals(10, $this->inventoryService->getStock($this->nonSerializedProduct->id));

        // Process return of 2 items
        $this->inventoryService->restoreStock($this->nonSerializedProduct->id, 2);
        $this->assertEquals(12, $this->inventoryService->getStock($this->nonSerializedProduct->id));
    }
}
