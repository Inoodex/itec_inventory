<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleServiceTest extends TestCase
{
    use RefreshDatabase;

    private SaleService $saleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->saleService = new SaleService(new InventoryService());
    }

    public function test_financial_calculations_correctly_sets_payable_and_status(): void
    {
        $data = [
            'subTotal'         => 1000,
            'discount'         => 100,
            'grandTotal'       => 900,
            'advanced_payment' => 300,
            'duePayment'       => 600,
        ];

        $financials = $this->saleService->calculateFinancials($data);

        $this->assertEquals(1000, $financials['total']);
        $this->assertEquals(100, $financials['discount']);
        $this->assertEquals(900, $financials['payble']);
        $this->assertEquals(300, $financials['advanced_payment']);
        $this->assertEquals(600, $financials['due_payment']);
        $this->assertEquals('partial', $financials['status']);
    }

    public function test_create_sale_creates_customer_items_and_deducts_inventory(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $brand = Brand::create(['name' => 'Brand A', 'status' => '1']);
        $category = Category::create(['name' => 'Cat A', 'status' => '1']);
        $product = Product::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Laptop',
            'model' => 'L-1',
            'status' => 1,
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'current_stock' => 10,
            'opening_stock' => 10,
        ]);

        $saleData = [
            'client_type'      => 'new',
            'name'             => 'John Doe',
            'phone'            => '01700000000',
            'address'          => 'Dhaka',
            'product'          => [$product->id],
            'qty'              => [2],
            'unit_price'       => [500],
            'subTotal'         => 1000,
            'discount'         => 0,
            'grandTotal'       => 1000,
            'advanced_payment' => 1000,
            'duePayment'       => 0,
        ];

        $sale = $this->saleService->createSale($saleData);

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'payble' => 1000,
            'status' => 'paid',
        ]);

        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'current_stock' => 8,
        ]);
    }
}
