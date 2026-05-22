<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Triggers for Inventory Updates
        DB::unprepared("DROP TRIGGER IF EXISTS after_sale_insert;");
        DB::unprepared("
            CREATE TRIGGER after_sale_insert
            AFTER INSERT ON sales
            FOR EACH ROW
            BEGIN
                UPDATE products 
                SET quantity = quantity - NEW.sold 
                WHERE id = NEW.product_id;

                INSERT INTO inventory_movements (product_id, employee_id, date, new_luto, pull_out, transaction_type, created_at, updated_at)
                VALUES (NEW.product_id, NEW.employee_id, NEW.date, 0, NEW.sold, 'SALE', NOW(), NOW());
            END;
        ");

        DB::unprepared("DROP TRIGGER IF EXISTS after_purchase_insert;");
        DB::unprepared("
            CREATE TRIGGER after_purchase_insert
            AFTER INSERT ON purchase_details
            FOR EACH ROW
            BEGIN
                DECLARE p_status VARCHAR(50);
                SELECT status INTO p_status FROM purchases WHERE id = NEW.purchase_id;
                
                IF p_status != 'Pending' AND p_status != 'Partial' THEN
                    UPDATE products SET quantity = quantity + NEW.quantity WHERE id = NEW.product_id;
                    INSERT INTO inventory_movements (product_id, employee_id, supplier_id, date, new_luto, pull_out, transaction_type, created_at, updated_at)
                    SELECT NEW.product_id, employee_id, supplier_id, NOW(), NEW.quantity, 0, 'PURCHASE', NOW(), NOW()
                    FROM purchases WHERE id = NEW.purchase_id;
                END IF;
            END;
        ");

        // 2. Views for Reports
        DB::unprepared("DROP VIEW IF EXISTS sales_summary_view;");
        DB::unprepared("
            CREATE VIEW sales_summary_view AS
            SELECT 
                p.product_name,
                c.category_name,
                SUM(s.sold) as total_quantity_sold,
                SUM(s.total_amount) as total_revenue,
                DATE(s.date) as sale_date
            FROM sales s
            JOIN products p ON s.product_id = p.id
            JOIN categories c ON p.category_id = c.id
            GROUP BY p.product_name, c.category_name, DATE(s.date);
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP VIEW IF EXISTS sales_summary_view;");
        DB::unprepared("DROP TRIGGER IF EXISTS after_purchase_insert;");
        DB::unprepared("DROP TRIGGER IF EXISTS after_sale_insert;");
    }
};
