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
        Schema::create('received_orders', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->foreignIdFor(\App\Models\PurchaseOrder::class);
            $table->foreignIdFor(\App\Models\RawMaterial::class);
            $table->foreignIdFor(\App\Models\Supplier::class);
            $table->decimal('received_quantity');
            $table->decimal('variance');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('received_orders');
    }
};
