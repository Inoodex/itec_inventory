<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\User;
use App\Models\WarrantyClaim;
use App\Services\WarrantyService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WarrantyServiceTest extends TestCase
{
    use DatabaseTransactions;

    private WarrantyService $warrantyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->warrantyService = new WarrantyService();
    }

    public function test_generate_claim_number_format(): void
    {
        $claimNo = $this->warrantyService->generateClaimNumber();
        $this->assertStringStartsWith('WC-' . date('Ymd') . '-', $claimNo);
    }

    public function test_create_warranty_claim_creates_claim_and_initial_log(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $customer = Customer::create([
            'name' => 'Alice Customer',
            'phone' => '01711111111',
            'status' => '1',
        ]);

        $brand = Brand::create(['name' => 'Brand B', 'status' => '1']);
        $category = Category::create(['name' => 'Cat B', 'status' => '1']);
        $product = Product::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Monitor 24 inch',
            'model' => 'M-24',
            'warranty' => 365,
            'status' => 1,
        ]);

        $sale = Sale::create([
            'order_no' => 'INV-99001',
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'qty' => 1,
            'total' => 15000,
            'payble' => 15000,
            'bill' => 15000,
            'status' => 'paid',
        ]);

        $salesItem = SalesItem::create([
            'order_id' => $sale->id,
            'product_id' => $product->id,
            'qty' => 1,
            'unit_price' => 15000,
            'sub_total' => 15000,
            'total_price' => 15000,
            'warranty' => 365,
        ]);

        $claim = $this->warrantyService->createClaim([
            'sales_item_id' => $salesItem->id,
            'serial_number' => 'SN-100293',
            'claim_date' => now()->toDateString(),
            'problem_description' => 'Display dead pixels in center',
        ]);

        $this->assertDatabaseHas('warranty_claims', [
            'id' => $claim->id,
            'sales_item_id' => $salesItem->id,
            'serial_number' => 'SN-100293',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('warranty_claim_logs', [
            'warranty_claim_id' => $claim->id,
            'status' => 'pending',
        ]);
    }

    public function test_update_claim_status_logs_transition(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $customer = Customer::create(['name' => 'Bob Customer', 'phone' => '01822222222', 'status' => '1']);
        $brand = Brand::create(['name' => 'Brand C', 'status' => '1']);
        $category = Category::create(['name' => 'Cat C', 'status' => '1']);
        $product = Product::create(['brand_id' => $brand->id, 'category_id' => $category->id, 'name' => 'GPU RTX 4060', 'model' => 'RTX4060', 'warranty' => 730, 'status' => 1]);
        $sale = Sale::create(['order_no' => 'INV-99002', 'customer_id' => $customer->id, 'product_id' => $product->id, 'qty' => 1, 'total' => 45000, 'payble' => 45000, 'bill' => 45000, 'status' => 'paid']);
        $salesItem = SalesItem::create(['order_id' => $sale->id, 'product_id' => $product->id, 'qty' => 1, 'unit_price' => 45000, 'sub_total' => 45000, 'total_price' => 45000, 'warranty' => 730]);

        $claim = $this->warrantyService->createClaim([
            'sales_item_id' => $salesItem->id,
            'claim_date' => now()->toDateString(),
            'problem_description' => 'No HDMI signal output',
        ]);

        $updatedClaim = $this->warrantyService->updateClaimStatus($claim, [
            'status' => 'replaced',
            'action_taken' => 'replacement',
            'replacement_serial_number' => 'SN-NEW-990812',
            'note' => 'Replaced with brand new unit from vendor stock.',
        ]);

        $this->assertEquals('replaced', $updatedClaim->status);
        $this->assertEquals('replacement', $updatedClaim->action_taken);
        $this->assertEquals('SN-NEW-990812', $updatedClaim->replacement_serial_number);

        $this->assertDatabaseHas('warranty_claim_logs', [
            'warranty_claim_id' => $claim->id,
            'status' => 'replaced',
            'note' => 'Replaced with brand new unit from vendor stock.',
        ]);
    }
}
