<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CustomerDueFifoPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_fifo_customer_payment_settles_oldest_sale_first(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'Sales Management']);
        Permission::create(['name' => 'Payment Management']);
        $user->givePermissionTo(['Sales Management', 'Payment Management']);
        $this->actingAs($user);

        $customer = Customer::create([
            'name' => 'Test Customer',
            'phone' => '01700000000',
            'email' => 'customer@test.com',
            'address' => 'Dhaka',
        ]);

        $brand = \App\Models\Brand::create(['name' => 'Brand A', 'status' => '1']);
        $category = \App\Models\Category::create(['name' => 'Cat A', 'status' => '1']);
        $product = \App\Models\Product::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Item A',
            'model' => 'M-1',
            'status' => 1,
            'selling_price' => 500,
            'purchase_price' => 400,
        ]);

        // Sale 1: Oldest (yesterday), payable 1000, paid 200, due 800
        $sale1 = Sale::create([
            'order_no' => 'INV-001',
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'qty' => 1,
            'total' => 1000,
            'sale_type' => 'retail',
            'bill' => 1000,
            'payble' => 1000,
            'advanced_payment' => 200,
            'due_payment' => 800,
            'status' => 'partial',
            'payment_status' => 1,
            'created_at' => now()->subDay(),
        ]);

        // Sale 2: Newer (today), payable 1500, paid 500, due 1000
        $sale2 = Sale::create([
            'order_no' => 'INV-002',
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'qty' => 1,
            'total' => 1500,
            'sale_type' => 'retail',
            'bill' => 1500,
            'payble' => 1500,
            'advanced_payment' => 500,
            'due_payment' => 1000,
            'status' => 'partial',
            'payment_status' => 1,
            'created_at' => now(),
        ]);

        // Access due payments page
        $response = $this->get(route('due-payments.index'));
        $response->assertStatus(200);
        $response->assertSee('Test Customer');
        $response->assertSee('2 Invoices');

        // Customer pays 1200 Tk (800 should clear Sale 1, 400 should go to Sale 2)
        $paymentResponse = $this->post(route('due-payments.customer-pay'), [
            'customer_id' => $customer->id,
            'payment_amount' => 1200,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
            'notes' => 'Bulk settlement test',
        ]);

        $paymentResponse->assertRedirect();
        $paymentResponse->assertSessionHas('success');

        // Refresh sales
        $sale1->refresh();
        $sale2->refresh();

        // Sale 1 should be completely paid
        $this->assertEquals(1000, $sale1->advanced_payment);
        $this->assertEquals(0, $sale1->due_payment);

        // Sale 2 should have 400 paid, 600 due
        $this->assertEquals(900, $sale2->advanced_payment);
        $this->assertEquals(600, $sale2->due_payment);

        // Verify Payment entries created
        $payments = Payment::where('customer_id', $customer->id)->get();
        $this->assertCount(2, $payments);
        $this->assertEquals(800, $payments->where('sale_id', $sale1->id)->first()->amount);
        $this->assertEquals(400, $payments->where('sale_id', $sale2->id)->first()->amount);
    }

    public function test_due_payments_page_displays_dual_role_badge_in_type_column(): void
    {
        $user = User::factory()->create();
        Permission::firstOrCreate(['name' => 'Payment Management']);
        $user->givePermissionTo(['Payment Management']);
        $this->actingAs($user);

        $customer = Customer::create([
            'name' => 'Dual Partner Co',
            'phone' => '01899999999',
            'email' => 'partner@test.com',
            'address' => 'Dhaka',
        ]);

        \App\Models\Vendor::create([
            'name' => 'Dual Partner Co',
            'phone' => '01899999999',
            'email' => 'partner@test.com',
            'address' => 'Dhaka',
        ]);

        $brand = \App\Models\Brand::create(['name' => 'Brand Test', 'status' => '1']);
        $category = \App\Models\Category::create(['name' => 'Cat Test', 'status' => '1']);
        $product = \App\Models\Product::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Product Test',
            'model' => 'PT-1',
            'status' => 1,
            'selling_price' => 500,
            'purchase_price' => 400,
        ]);

        Sale::create([
            'order_no' => 'INV-DUAL-001',
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'qty' => 1,
            'bill' => 1000,
            'total' => 1000,
            'sub_total' => 1000,
            'payble' => 1000,
            'advanced_payment' => 0,
            'due_payment' => 1000,
            'selling_type' => 'retail',
            'payment_status' => 'due',
            'created_at' => now(),
        ]);

        $response = $this->get(route('due-payments.index'));
        $response->assertOk();
        $response->assertSee('Dual-Role');
    }
}
