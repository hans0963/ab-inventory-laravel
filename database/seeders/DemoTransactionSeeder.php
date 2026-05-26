<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerCreditPayment;
use App\Models\DiscountType;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductionIn;
use App\Models\ProductionInItem;
use App\Models\ProductionOut;
use App\Models\ProductionOutItem;
use App\Models\RawMaterial;
use App\Models\Sale;
use App\Models\StockWithdrawal;
use App\Models\StockWithdrawalItem;
use App\Models\User;
use App\Services\StockMovementLogger;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoTransactionSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $admin = User::where('email', 'admin@example.com')->firstOrFail();
            $manager = User::where('email', 'manager@example.com')->firstOrFail();
            $cashierUser = User::where('email', 'cashier@example.com')->firstOrFail();
            $cashier = Employee::where('user_id', $cashierUser->id)->firstOrFail();

            $this->seedProductionIn($admin, $manager);
            $this->seedProductionOut($admin, $manager);
            $this->seedStockWithdrawal($admin, $manager);
            $this->seedSales($cashier, $cashierUser);
            $this->seedOrder($cashier);
        });
    }

    private function seedProductionIn(User $creator, User $approver): void
    {
        $productionIn = ProductionIn::firstOrCreate(
            ['production_in_no' => 'PIN-DEMO-001'],
            [
                'date' => now()->subDays(2)->toDateString(),
                'notes' => 'Morning production batch',
                'total_inventory_value' => 0,
                'status' => 'Pending',
                'created_by' => $creator->id,
                'created_date' => now()->subDays(2)->toDateString(),
            ]
        );

        if (!$productionIn->wasRecentlyCreated) {
            return;
        }

        $items = [
            'Pandesal' => 40,
            'Ensaymada' => 20,
            'Chocolate Cake' => 3,
        ];

        foreach ($items as $productName => $quantity) {
            $product = Product::where('product_name', $productName)->firstOrFail();
            ProductionInItem::create([
                'production_in_id' => $productionIn->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->selling_price,
                'total_value' => $quantity * $product->selling_price,
                'expiration_date' => now()->addDays(3)->toDateString(),
            ]);
        }

        $productionIn->calculateTotalValue();
        $productionIn->approve($approver);
    }

    private function seedProductionOut(User $creator, User $approver): void
    {
        $productionOut = ProductionOut::firstOrCreate(
            ['production_out_no' => 'POUT-DEMO-001'],
            [
                'date' => now()->subDay()->toDateString(),
                'reason' => 'Quality check wastage',
                'notes' => 'Sample spoiled items from display batch',
                'total_inventory_value' => 0,
                'status' => 'Pending',
                'created_by' => $creator->id,
                'created_date' => now()->subDay()->toDateString(),
            ]
        );

        if (!$productionOut->wasRecentlyCreated) {
            return;
        }

        $pandesal = Product::where('product_name', 'Pandesal')->firstOrFail();
        ProductionOutItem::create([
            'production_out_id' => $productionOut->id,
            'product_id' => $pandesal->id,
            'quantity' => 5,
            'unit_price' => $pandesal->selling_price,
            'total_value' => 5 * $pandesal->selling_price,
        ]);

        $productionOut->total_inventory_value = $productionOut->items()->sum('total_value');
        $productionOut->save();
        $productionOut->approve($approver);
    }

    private function seedStockWithdrawal(User $creator, User $approver): void
    {
        $withdrawal = StockWithdrawal::firstOrCreate(
            ['withdrawal_no' => 'WD-DEMO-001'],
            [
                'date' => now()->subDay()->toDateString(),
                'reason' => 'Internal Use',
                'notes' => 'Packaging used for sample giveaway',
                'total_quantity' => 10,
                'total_value' => 50,
                'status' => 'Pending',
                'created_by' => $creator->id,
                'created_date' => now()->subDay()->toDateString(),
            ]
        );

        if (!$withdrawal->wasRecentlyCreated) {
            return;
        }

        $box = RawMaterial::where('material_name', 'Cake Box 8x8')->firstOrFail();
        StockWithdrawalItem::create([
            'stock_withdrawal_id' => $withdrawal->id,
            'raw_material_id' => $box->id,
            'quantity' => 10,
            'unit_price' => 5,
            'total_value' => 50,
        ]);

        $withdrawal->approve($approver);
    }

    private function seedSales(Employee $cashier, User $cashierUser): void
    {
        if (Sale::where('receipt_number', 'RCP-DEMO-001')->exists()) {
            return;
        }

        $customer = Customer::where('name', 'Maria Santos')->firstOrFail();
        $discount = DiscountType::where('discount_name', 'Senior/PWD Discount')->first();
        $lines = [
            ['product' => 'Pandesal', 'qty' => 10],
            ['product' => 'Ensaymada', 'qty' => 4],
            ['product' => 'Chocolate Cake', 'qty' => 1],
        ];

        $subtotal = collect($lines)->sum(function ($line) {
            $product = Product::where('product_name', $line['product'])->firstOrFail();
            return (float) $product->selling_price * $line['qty'];
        });
        $discountAmount = round($subtotal * 0.20, 2);
        $afterDiscount = $subtotal - $discountAmount;
        $vatRate = 12;
        $vatAmount = round($afterDiscount - ($afterDiscount / 1.12), 2);
        $netSubtotal = round($afterDiscount - $vatAmount, 2);
        $grandTotal = round($afterDiscount, 2);

        $remainingDiscount = $discountAmount;
        $remainingVat = $vatAmount;
        $remainingNetSubtotal = $netSubtotal;
        $remainingTotal = $grandTotal;

        foreach ($lines as $index => $line) {
            $product = Product::where('product_name', $line['product'])->lockForUpdate()->firstOrFail();
            $lineSubtotal = (float) $product->selling_price * $line['qty'];
            $ratio = $subtotal > 0 ? $lineSubtotal / $subtotal : 0;
            $isLast = $index === array_key_last($lines);

            $lineDiscount = $isLast ? $remainingDiscount : round($discountAmount * $ratio, 2);
            $lineVat = $isLast ? $remainingVat : round($vatAmount * $ratio, 2);
            $lineNetSubtotal = $isLast ? $remainingNetSubtotal : round($netSubtotal * $ratio, 2);
            $lineTotal = $isLast ? $remainingTotal : round($grandTotal * $ratio, 2);

            $sale = Sale::create([
                'product_id' => $product->id,
                'employee_id' => $cashier->id,
                'customer_id' => $customer->id,
                'date' => now()->toDateString(),
                'sold' => $line['qty'],
                'unit_price' => $product->selling_price,
                'discount_type_id' => $discount?->id,
                'discount_amount' => $lineDiscount,
                'payment_type' => 'Cash',
                'vat_rate' => $vatRate,
                'vat_type' => 'Inclusive',
                'vat_amount' => $lineVat,
                'subtotal_amount' => $lineNetSubtotal,
                'total_amount' => $lineTotal,
                'receipt_number' => 'RCP-DEMO-001',
            ]);

            $quantityBefore = $product->quantity;
            $product->decrement('quantity', $line['qty']);
            StockMovementLogger::record($product, $quantityBefore, -$line['qty'], 'OUT', 'SALE', $sale, $cashier->id, $cashierUser->id);

            $remainingDiscount -= $lineDiscount;
            $remainingVat -= $lineVat;
            $remainingNetSubtotal -= $lineNetSubtotal;
            $remainingTotal -= $lineTotal;
        }

        $creditCustomer = Customer::where('name', 'Cafe Luna')->firstOrFail();
        CustomerCreditPayment::updateOrCreate(
            ['customer_id' => $creditCustomer->id, 'payment_date' => now()->toDateString()],
            [
                'sale_id' => null,
                'amount' => 1500,
                'payment_method' => 'Cash',
                'notes' => 'Seeded sample credit payment',
                'received_by' => $cashierUser->id,
            ]
        );
    }

    private function seedOrder(Employee $cashier): void
    {
        $customer = Customer::where('name', 'Cafe Luna')->firstOrFail();
        $order = Order::firstOrCreate(
            [
                'customer_id' => $customer->id,
                'order_date' => now()->addDay()->toDateString(),
                'order_status' => 'Pending',
            ],
            [
                'employee_id' => $cashier->id,
                'total' => 1300,
                'payment_type' => 'Credit/Loan',
                'total_products' => 2,
            ]
        );

        if (!$order->wasRecentlyCreated) {
            return;
        }

        $cake = Product::where('product_name', 'Chocolate Cake')->firstOrFail();
        OrderDetail::create([
            'order_id' => $order->id,
            'product_id' => $cake->id,
            'quantity' => 2,
            'unit_cost' => $cake->selling_price,
            'total' => 2 * $cake->selling_price,
        ]);
    }
}
