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
    }
};
