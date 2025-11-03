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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->foreignIdFor(\App\Models\Shop::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\ProductOrders::class);
            $table->json('products'); 
            $table->string('distance');
            $table->string('approximate_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
