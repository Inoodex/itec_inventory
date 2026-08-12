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
        if (!Schema::hasTable('warranty_claims')) {
            Schema::create('warranty_claims', function (Blueprint $table) {
                $table->id();
                $table->string('claim_no')->unique(); // e.g. WC-20260730-001
                $table->foreignId('sale_id')->constrained()->onDelete('cascade');
                $table->foreignId('sales_item_id')->constrained('sales_items')->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
                
                $table->string('serial_number')->nullable();
                $table->date('claim_date');
                $table->date('warranty_expiry_date');
                $table->text('problem_description');
                $table->text('condition_notes')->nullable();
                
                $table->enum('status', [
                    'pending',
                    'under_inspection',
                    'sent_to_vendor',
                    'repaired',
                    'replaced',
                    'rejected',
                    'completed'
                ])->default('pending');
                
                $table->enum('action_taken', [
                    'none',
                    'repair',
                    'replacement',
                    'refund'
                ])->default('none');
                
                $table->string('replacement_serial_number')->nullable();
                $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('resolved_at')->nullable();
                $table->text('remarks')->nullable();
                
                $table->softDeletes();
                $table->timestamps();

                $table->index(['claim_no', 'status']);
                $table->index(['sale_id', 'product_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_claims');
    }
};
