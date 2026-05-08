<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // 1. Drop the old purchase trigger
        DB::unprepared("DROP TRIGGER IF EXISTS after_purchase_insert;");

        // 2. Re-create after_purchase_insert with stock update logic
        DB::unprepared("
            CREATE TRIGGER after_purchase_insert
            AFTER INSERT ON purchase_details
            FOR EACH ROW
            BEGIN
                -- Update product quantity
                UPDATE products 
                SET quantity = quantity + NEW.quantity 
                WHERE id = NEW.product_id;

                -- Insert into inventory_movements
                INSERT INTO inventory_movements (
                    product_id, 
                    employee_id, 
                    supplier_id, 
                    date, 
                    new_luto, 
                    pull_out,
                    transaction_type,
                    created_at,
                    updated_at
                )
                SELECT 
                    NEW.product_id, 
                    (SELECT employee_id FROM purchases WHERE id = NEW.purchase_id),
                    (SELECT supplier_id FROM purchases WHERE id = NEW.purchase_id), 
                    NOW(), 
                    NEW.quantity, 
                    0,
                    'PURCHASE',
                    NOW(),
                    NOW();
            END;
        ");

        // 3. Update the before_inventory_insert trigger to be more robust
        DB::unprepared("DROP TRIGGER IF EXISTS before_inventory_insert;");
        DB::unprepared("
            CREATE TRIGGER before_inventory_insert
            BEFORE INSERT ON inventory_movements
            FOR EACH ROW
            BEGIN
                DECLARE last_balance INT;

                -- Get last inventory balance
                SELECT total_inventory INTO last_balance 
                FROM inventory_movements 
                WHERE product_id = NEW.product_id 
                ORDER BY id DESC LIMIT 1;

                -- Set new balance calculation
                SET NEW.balance_forwarded = IFNULL(last_balance, 0);
                SET NEW.new_balance = NEW.balance_forwarded + NEW.new_luto - NEW.pull_out;
                SET NEW.total_inventory = NEW.new_balance;
            END;
        ");

        // 4. Drop the old sales trigger and re-create it consistently
        DB::unprepared("DROP TRIGGER IF EXISTS after_sales_insert;");
        DB::unprepared("
            CREATE TRIGGER after_sales_insert
            AFTER INSERT ON sales
            FOR EACH ROW
            BEGIN
                -- Update product quantity
                UPDATE products 
                SET quantity = quantity - NEW.sold 
                WHERE id = NEW.product_id;

                -- Insert into inventory_movements
                INSERT INTO inventory_movements (
                    product_id, 
                    employee_id, 
                    date, 
                    new_luto, 
                    pull_out,
                    transaction_type,
                    created_at,
                    updated_at
                )
                VALUES (
                    NEW.product_id, 
                    NEW.employee_id,
                    NOW(), 
                    0, 
                    NEW.sold,
                    'SALE',
                    NOW(),
                    NOW()
                );
            END;
        ");
    }

    public function down()
    {
        // Reverse to the simpler triggers if needed
        DB::unprepared("DROP TRIGGER IF EXISTS after_purchase_insert;");
        DB::unprepared("DROP TRIGGER IF EXISTS after_sales_insert;");
    }
};
