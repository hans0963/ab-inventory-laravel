<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // We only want to update stock when a purchase is Approved or Complete, 
        // not during PO creation (Pending).
        // However, the current trigger is on purchase_details insert.
        // Let's modify it to only trigger if the parent purchase is NOT 'Pending'.
        
        DB::unprepared("DROP TRIGGER IF EXISTS after_purchase_insert;");

        DB::unprepared("
            CREATE TRIGGER after_purchase_insert
            AFTER INSERT ON purchase_details
            FOR EACH ROW
            BEGIN
                DECLARE p_status VARCHAR(50);
                
                SELECT status INTO p_status FROM purchases WHERE id = NEW.purchase_id;
                
                -- Only update stock if PO is already Approved or Complete 
                -- (Traditional purchase flow without receiving module)
                IF p_status != 'Pending' AND p_status != 'Partial' THEN
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
                END IF;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the version that always updates stock
        DB::unprepared("DROP TRIGGER IF EXISTS after_purchase_insert;");
        DB::unprepared("
            CREATE TRIGGER after_purchase_insert
            AFTER INSERT ON purchase_details
            FOR EACH ROW
            BEGIN
                UPDATE products 
                SET quantity = quantity + NEW.quantity 
                WHERE id = NEW.product_id;

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
    }
};
