<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\Purchase;
use App\Models\InventoryReceiving;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseReceivingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_purchase_and_receiving_flow()
    {
        $admin = User::factory()->create(['role' => 'manager']);
        $supplier = Supplier::factory()->create(['status' => 'Active']);
        $employee = Employee::factory()->create();
        $product = Product::factory()->create([
            'inventory_type' => 'Raw Material',
            'quantity' => 10,
            'status' => 'Active'
        ]);

        // 1. Create Purchase Order
        $response = $this->actingAs($admin)->post(route('purchases.store'), [
            'purchase_date' => now()->toDateString(),
            'expected_delivery_date' => now()->addDays(3)->toDateString(),
            'supplier_id' => $supplier->id,
            'employee_id' => $employee->id,
            'product_id' => [$product->id],
            'quantity' => [100],
            'price' => [50.00],
            'notes' => 'Test PO'
        ]);

        if ($response->exception) {
            $this->fail($response->exception->getMessage());
        }

        $purchase = Purchase::first();
        if (!$purchase) {
            $this->fail('Purchase record was not created. Session errors: ' . json_encode(session('errors') ? session('errors')->getMessages() : []));
        }
        $this->assertNotNull($purchase->po_number);
        $this->assertEquals('Pending', $purchase->status);
        $this->assertEquals(now()->addDays(3)->toDateString(), $purchase->expected_delivery_date->toDateString());

        // 2. Record Receiving
        $response = $this->actingAs($admin)->post(route('inventory-receiving.store'), [
            'date' => now()->toDateString(),
            'supplier_id' => $supplier->id,
            'purchase_id' => $purchase->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity_ordered' => 100,
                    'quantity_received' => 95, // Discrepancy
                    'unit_cost' => 50.00,
                    'condition' => 'Good'
                ]
            ]
        ]);

        if ($response->exception) {
            $this->fail($response->exception->getMessage());
        }

        $receiving = InventoryReceiving::first();
        if (!$receiving) {
            $this->fail('Receiving record was not created. Session errors: ' . json_encode(session('errors') ? session('errors')->getMessages() : []));
        }
        $this->assertEquals('Pending', $receiving->status);

        // 3. Link Receiving to PO
        $this->actingAs($admin)->post(route('purchases.link-receiving', $purchase->id), [
            'inventory_receiving_id' => $receiving->id
        ]);

        $purchase->refresh();
        $this->assertEquals('Partial', $purchase->status); // 95/100 received

        // 4. Approve Receiving and Check Stock
        $this->actingAs($admin)->post(route('inventory-receiving.approve', $receiving->id));
        
        $product->refresh();
        $this->assertEquals(105, $product->quantity); // 10 + 95

        $receiving->refresh();
        $this->assertEquals('Approved', $receiving->status);
    }
}
