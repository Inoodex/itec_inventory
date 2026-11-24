<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\User;
use App\Models\Vendor;
use App\Services\WarrantyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class VendorDueAndPartyLedgerTest extends TestCase
{
    use RefreshDatabase;

    private function setupPermissions(User $user): void
    {
        Permission::create(['name' => 'Sales Management']);
        Permission::create(['name' => 'Payment Management']);
        Permission::create(['name' => 'Accounts Management']);
        Permission::create(['name' => 'Warranty Management']);
        $user->givePermissionTo(['Sales Management', 'Payment Management', 'Accounts Management', 'Warranty Management']);
    }

    public function test_vendor_due_fifo_payment_settles_oldest_purchase_first(): void
    {
        $user = User::factory()->create();
        $this->setupPermissions($user);
        $this->actingAs($user);

        $vendor = Vendor::create([
            'name' => 'Digi Mark Solution',
            'phone' => '01819234343',
            'email' => 'digi@vendor.com',
            'address' => 'Dhaka',
        ]);

        $brand = Brand::create(['name' => 'HP', 'status' => '1']);
        $category = Category::create(['name' => 'Printers', 'status' => '1']);
        $product = Product::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'HP Laser Printer',
            'model' => 'P-101',
            'status' => 1,
            'selling_price' => 15000,
            'purchase_price' => 12000,
        ]);

        // Purchase 1: Oldest, total 10000, paid 2000, due 8000
        $pur1 = Purchase::create([
            'vendor_id' => $vendor->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 10000,
            'total_price' => 10000,
            'payment' => 2000,
            'due' => 8000,
            'created_at' => now()->subDays(5),
        ]);

        // Purchase 2: Newer, total 15000, paid 5000, due 10000
        $pur2 = Purchase::create([
            'vendor_id' => $vendor->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 15000,
            'total_price' => 15000,
            'payment' => 5000,
            'due' => 10000,
            'created_at' => now(),
        ]);

        // Access vendor due page
        $res = $this->get(route('vendor-due.index'));
        $res->assertStatus(200);
        $res->assertSee('Digi Mark Solution');
        $res->assertSee('2 Bills');

        // Disburse 12000 Tk (8000 clears pur1, 4000 goes to pur2)
        $payRes = $this->post(route('vendor-due.fifo-pay'), [
            'vendor_id' => $vendor->id,
            'payment_amount' => 12000,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
            'notes' => 'FIFO test',
        ]);

        $payRes->assertRedirect();
        $payRes->assertSessionHas('success');

        $pur1->refresh();
        $pur2->refresh();

        $this->assertEquals(0, $pur1->due);
        $this->assertEquals(10000, $pur1->payment);

        $this->assertEquals(6000, $pur2->due);
        $this->assertEquals(9000, $pur2->payment);
    }

    public function test_dual_role_party_ledger_and_contra_settlement(): void
    {
        $user = User::factory()->create();
        $this->setupPermissions($user);
        $this->actingAs($user);

        // Same person as Customer and Vendor (matched by phone 01819234343)
        $customer = Customer::create([
            'name' => 'Digi Mark Solution',
            'phone' => '01819234343',
            'email' => 'digi@test.com',
        ]);

        $vendor = Vendor::create([
            'name' => 'Digi Mark Solution',
            'phone' => '01819234343',
            'email' => 'digi@test.com',
            'address' => 'Dhaka',
        ]);

        $brand = Brand::create(['name' => 'Canon', 'status' => '1']);
        $category = Category::create(['name' => 'Printers', 'status' => '1']);
        $product = Product::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Canon Printer',
            'model' => 'C-200',
            'status' => 1,
            'selling_price' => 20000,
            'purchase_price' => 15000,
        ]);

        // Customer owes us 10,000 Tk
        $sale = Sale::create([
            'order_no' => 'INV-CONTRA-1',
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'qty' => 1,
            'total' => 10000,
            'sale_type' => 'retail',
            'bill' => 10000,
            'payble' => 10000,
            'advanced_payment' => 0,
            'due_payment' => 10000,
            'status' => 'credit',
            'payment_status' => 1,
        ]);

        // We owe Vendor 6,000 Tk
        $purchase = Purchase::create([
            'vendor_id' => $vendor->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 6000,
            'total_price' => 6000,
            'payment' => 0,
            'due' => 6000,
        ]);

        // View party ledger
        $ledgerRes = $this->get(route('party-ledger.show', ['type' => 'customer', 'id' => $customer->id]));
        $ledgerRes->assertStatus(200);
        $ledgerRes->assertSee('Digi Mark Solution');
        $ledgerRes->assertSee('Net Receivable');

        // Apply Contra Settlement of 6,000 Tk (offset vendor due completely, customer due becomes 4,000)
        $contraRes = $this->post(route('party-ledger.contra-settle'), [
            'customer_id' => $customer->id,
            'vendor_id' => $vendor->id,
            'settlement_amount' => 6000,
            'settlement_date' => now()->format('Y-m-d'),
            'notes' => 'Mutual Contra Settlement',
        ]);

        $contraRes->assertRedirect();
        $contraRes->assertSessionHas('success');

        $sale->refresh();
        $purchase->refresh();

        // Customer due should now be 4,000 (10,000 - 6,000)
        $this->assertEquals(4000, $sale->due_payment);
        $this->assertEquals(6000, $sale->advanced_payment);

        // Vendor due should now be 0 (6,000 - 6,000)
        $this->assertEquals(0, $purchase->due);
        $this->assertEquals(6000, $purchase->payment);
    }

    public function test_warranty_claim_succeeds_even_when_item_warranty_is_zero(): void
    {
        $user = User::factory()->create();
        $this->setupPermissions($user);
        $this->actingAs($user);

        $customer = Customer::create(['name' => 'Warranty Customer', 'phone' => '01911111111']);
        $brand = Brand::create(['name' => 'Epson', 'status' => '1']);
        $category = Category::create(['name' => 'Printers', 'status' => '1']);
        $product = Product::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Epson L3210 Printer',
            'model' => 'L3210',
            'warranty' => 0, // Product warranty is 0
            'status' => 1,
            'selling_price' => 18000,
            'purchase_price' => 15000,
        ]);

        $sale = Sale::create([
            'order_no' => 'INV-WAR-001',
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'qty' => 1,
            'total' => 18000,
            'sale_type' => 'retail',
            'bill' => 18000,
            'payble' => 18000,
            'advanced_payment' => 18000,
            'due_payment' => 0,
            'status' => 'paid',
            'payment_status' => 1,
            'created_at' => now()->subDays(60), // Sold 60 days ago
        ]);

        $salesItem = SalesItem::create([
            'order_id' => $sale->id,
            'product_id' => $product->id,
            'unit_price' => 18000,
            'warranty' => 0, // Sale item warranty is 0
            'qty' => 1,
            'total_price' => 18000,
            'returned_qty' => 0,
        ]);

        // Search eligible sale items by product name
        $service = app(WarrantyService::class);
        $results = $service->searchEligibleSaleItems('Epson');
        $this->assertNotEmpty($results);

        // Submit claim with manual serial and override
        $claimRes = $this->post(route('warranties.store'), [
            'sales_item_id' => $salesItem->id,
            'serial_number' => 'EPSON-SN-998877',
            'claim_date' => now()->format('Y-m-d'),
            'problem_description' => 'Paper jam issue and red light blinking',
            'condition_notes' => 'Clean body',
            'override_warranty' => 1,
        ]);

        $claimRes->assertRedirect();
        $claimRes->assertSessionHas('success');

        $this->assertDatabaseHas('warranty_claims', [
            'sales_item_id' => $salesItem->id,
            'serial_number' => 'EPSON-SN-998877',
            'status' => 'pending',
        ]);
    }
}
