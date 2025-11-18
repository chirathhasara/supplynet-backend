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
        Schema::create('accept_product_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Shop::class);
            $table->foreignIdFor(\App\Models\Delivery::class);
            $table->json('received_products');
            $table->json('accepted_products');
            $table->json('rejected_products');
            $table->text('note');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accept_product_orders');
    }
};
