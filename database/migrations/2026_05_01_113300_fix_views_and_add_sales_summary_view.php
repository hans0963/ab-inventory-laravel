<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing views and triggers first
        DB::unprepared("DROP VIEW IF EXISTS vw_recent_sales");
        DB::unprepared("DROP VIEW IF EXISTS vw_best_selling_products");
        DB::unprepared("DROP VIEW IF EXISTS vw_low_stock_alerts");
        DB::unprepared("DROP VIEW IF EXISTS vw_sales_summary");
        DB::unprepared("DROP TRIGGER IF EXISTS after_order_details_insert");

        // 1. Re-create vw_recent_sales with correct join logic
        DB::unprepared("
            CREATE VIEW vw_recent_sales AS
            SELECT 
                o.id AS order_id,
                o.order_date,
                c.name AS customer_name,
                c.phone AS customer_phone,
                c.address AS customer_address,
                e.employee_name AS sold_by,
                SUM(od.quantity) AS total_quantity_sold,
                SUM(od.total) AS total_sales_value
            FROM orders o
            JOIN customers c ON o.customer_id = c.id
            JOIN employees e ON o.employee_id = e.id
            JOIN order_details od ON o.id = od.order_id
            GROUP BY 
                o.id, 
                o.order_date, 
                c.name, 
                c.phone, 
                c.address, 
                e.employee_name
        ");

        // 2. Re-create vw_best_selling_products
        DB::unprepared("
            CREATE VIEW vw_best_selling_products AS
            SELECT 
                od.product_id AS ProductID,
                p.product_name,
                c.category_name,
                SUM(od.quantity) AS total_sold,
                SUM(od.total) AS total_revenue
            FROM order_details od
            JOIN products p ON od.product_id = p.id
            LEFT JOIN categories c ON p.category_id = c.id
            GROUP BY od.product_id, p.product_name, c.category_name
            ORDER BY total_sold DESC
        ");

        // 3. Re-create vw_low_stock_alerts
        DB::unprepared("
            CREATE VIEW vw_low_stock_alerts AS
            SELECT 
                p.id AS ProductID,
                p.product_name,
                c.category_name,
                p.quantity AS current_stock,
                p.stock_alert_threshold,
                (p.stock_alert_threshold - p.quantity) AS stock_deficit
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.quantity <= p.stock_alert_threshold
        ");

        // 4. Create the missing vw_sales_summary view
        DB::unprepared("
            CREATE VIEW vw_sales_summary AS
            SELECT 
                SUM(total) AS total_revenue,
                COUNT(id) AS total_orders,
                SUM(total_products) AS total_products_sold
            FROM orders
        ");

        // 5. Re-create the after_order_details_insert trigger with improved logic
        DB::unprepared("
            CREATE TRIGGER after_order_details_insert
            AFTER INSERT ON order_details
            FOR EACH ROW
            BEGIN
                DECLARE product_qty INT;
                DECLARE new_inventory INT;
                DECLARE order_emp_id INT;

                -- Get current stock
                SELECT quantity INTO product_qty FROM products WHERE id = NEW.product_id;

                -- Get employee ID from the parent order
                SELECT employee_id INTO order_emp_id FROM orders WHERE id = NEW.order_id;

                -- Deduct stock
                SET new_inventory = product_qty - NEW.quantity;

                -- Update the product stock
                UPDATE products 
                SET quantity = new_inventory 
                WHERE id = NEW.product_id;

                -- Insert into inventory_movements
                INSERT INTO inventory_movements (
                    product_id, 
                    employee_id, 
                    date, 
                    balance_forwarded, 
                    new_luto, 
                    pull_out, 
                    total_inventory,
                    transaction_type,
                    created_at,
                    updated_at
                )
                VALUES (
                    NEW.product_id,
                    order_emp_id,
                    NOW(),
                    product_qty,
                    0,
                    NEW.quantity,
                    new_inventory,
                    'SALE',
                    NOW(),
                    NOW()
                );
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP VIEW IF EXISTS vw_recent_sales");
        DB::unprepared("DROP VIEW IF EXISTS vw_best_selling_products");
        DB::unprepared("DROP VIEW IF EXISTS vw_low_stock_alerts");
        DB::unprepared("DROP VIEW IF EXISTS vw_sales_summary");
        DB::unprepared("DROP TRIGGER IF EXISTS after_order_details_insert");
    }
};
