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
        Schema::create('received_purchase_orders', function (Blueprint $table) {
            $table->id();
            
            // Link to purchase order
            $table->foreignIdFor(\App\Models\PurchaseOrder::class)->constrained();
            $table->foreignIdFor(\App\Models\RawMaterial::class);

            // Quantities
            $table->decimal('requested_units', 10, 2);   // originally 'request_units '
            $table->decimal('received_units', 10, 2);
            $table->decimal('unit_shortage', 10, 2)->default(0); // auto-calculated maybe

            // Financial loss due to shortage or damage
            $table->decimal('loss_amount', 10, 2)->default(0);

            // Receiving information
            $table->dateTime('due_date');
            $table->dateTime('received_date');
            $table->integer('no_of_late_days');
            
            
            // Quality & additional data
            $table->string('quality_status')->default('good'); // good / damaged / expired
            $table->string('batch_no')->nullable();
            $table->date('expiry_date')->nullable();

            // Optional attachment (invoice, GRN)
            $table->string('attachment')->nullable();

            // Explanation
            $table->text('note')->nullable();
            $table->text('difference_reason')->nullable();

            // Status of receiving
            $table->string('status')->default('received'); // received / partially_received / pending

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('received_purchase_orders');
    }
};
