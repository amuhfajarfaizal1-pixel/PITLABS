<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('rental_orders', 'picked_up_at')) $table->dateTime('picked_up_at')->nullable()->after('customer_terms_accepted_at');
            if (! Schema::hasColumn('rental_orders', 'returned_at')) $table->dateTime('returned_at')->nullable()->after('picked_up_at');
        });

        if (! Schema::hasTable('rental_inspections')) {
            Schema::create('rental_inspections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('rental_orders');
                $table->foreignId('order_item_id')->constrained('rental_order_items');
                $table->foreignId('unit_id')->nullable()->constrained('inventory_units');
                $table->foreignId('inspector_user_id')->constrained('users');
                $table->enum('condition_status', ['GOOD','MINOR_ISSUE','MAJOR_ISSUE','DAMAGED','LOST']);
                $table->text('inspection_notes');
                $table->text('completeness_notes')->nullable();
                $table->string('evidence_path', 500)->nullable();
                $table->timestamp('inspected_at')->useCurrent();
                $table->timestamps();
                $table->unique('order_item_id', 'uq_inspection_order_item');
                $table->index(['condition_status','inspected_at'], 'idx_inspection_condition');
            });
        }

        if (! Schema::hasTable('rental_fines')) {
            Schema::create('rental_fines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('rental_orders');
                $table->foreignId('inspection_id')->nullable()->constrained('rental_inspections')->nullOnDelete();
                $table->enum('fine_type', ['LATE','DAMAGE','LOSS','OTHER']);
                $table->text('description');
                $table->decimal('amount', 14, 2)->nullable();
                $table->enum('fine_status', ['PENDING_REVIEW','ASSESSED','PAID','WAIVED'])->default('PENDING_REVIEW');
                $table->foreignId('assessed_by')->nullable()->constrained('users');
                $table->timestamp('assessed_at')->nullable();
                $table->foreignId('settled_by')->nullable()->constrained('users');
                $table->timestamp('settled_at')->nullable();
                $table->text('admin_note')->nullable();
                $table->timestamps();
                $table->index(['fine_status','created_at'], 'idx_fines_status');
                $table->index(['order_id','fine_type'], 'idx_fines_order_type');
            });
        }

        if (! Schema::hasTable('rental_waitlist_entries')) {
            Schema::create('rental_waitlist_entries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users');
                $table->foreignId('product_id')->constrained('products');
                $table->dateTime('start_at');
                $table->dateTime('end_at');
                $table->enum('status', ['WAITING','NOTIFIED','FULFILLED','CANCELLED'])->default('WAITING');
                $table->timestamps();
                $table->unique(['user_id','product_id','start_at','end_at'], 'uq_waitlist_customer_period');
                $table->index(['status','start_at'], 'idx_waitlist_status_start');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_waitlist_entries');
        Schema::dropIfExists('rental_fines');
        Schema::dropIfExists('rental_inspections');
        Schema::table('rental_orders', function (Blueprint $table) {
            if (Schema::hasColumn('rental_orders', 'returned_at')) $table->dropColumn('returned_at');
            if (Schema::hasColumn('rental_orders', 'picked_up_at')) $table->dropColumn('picked_up_at');
        });
    }
};
