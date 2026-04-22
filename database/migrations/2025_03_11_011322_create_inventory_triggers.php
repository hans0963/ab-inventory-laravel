<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // 🔹 1. Deduct stock when a sale occurs
        DB::unprepared("
            CREATE TRIGGER after_sales_insert
            AFTER INSERT ON sales
            FOR EACH ROW
            BEGIN
                INSERT INTO inventory_movements (product_id, employee_id, supplier_id, date, balance_forwarded, new_luto, pull_out, total_inventory)
                SELECT 
                    NEW.product_id, 
                    (SELECT id FROM employees ORDER BY id LIMIT 1), -- Replace with actual employee logic
                    NULL, -- No supplier involved in sales
                    NOW(), 
                    IFNULL((SELECT total_inventory FROM inventory_movements WHERE product_id = NEW.product_id ORDER BY id DESC LIMIT 1), 0),
                    0, 
                    NEW.sold, 
                    IFNULL((SELECT total_inventory FROM inventory_movements WHERE product_id = NEW.product_id ORDER BY id DESC LIMIT 1), 0) - NEW.sold;
            END;
        ");

        // 🔹 2. Add stock when a purchase is made
        DB::unprepared("
            CREATE TRIGGER after_purchase_insert
            AFTER INSERT ON purchase_details
            FOR EACH ROW
            BEGIN
                INSERT INTO inventory_movements (product_id, employee_id, supplier_id, date, balance_forwarded, new_luto, pull_out, total_inventory)
                SELECT 
                    NEW.product_id, 
                    (SELECT id FROM employees ORDER BY id LIMIT 1), -- Replace with actual employee logic
                    (SELECT supplier_id FROM purchases WHERE id = NEW.purchase_id), 
                    NOW(), 
                    IFNULL((SELECT total_inventory FROM inventory_movements WHERE product_id = NEW.product_id ORDER BY id DESC LIMIT 1), 0),
                    NEW.quantity, 
                    0, 
                    IFNULL((SELECT total_inventory FROM inventory_movements WHERE product_id = NEW.product_id ORDER BY id DESC LIMIT 1), 0) + NEW.quantity;
            END;
        ");

        // 🔹 3. Auto-calculate stock balance on inventory movements
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

                -- Set new balance
                SET NEW.balance_forwarded = IFNULL(last_balance, 0);
                SET NEW.total_inventory = NEW.balance_forwarded + NEW.new_luto - NEW.pull_out;
            END;
        ");
    }

    public function down()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS after_sales_insert;");
        DB::unprepared("DROP TRIGGER IF EXISTS after_purchase_insert;");
        DB::unprepared("DROP TRIGGER IF EXISTS before_inventory_insert;");
    }
};
