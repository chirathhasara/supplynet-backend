<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get random shop and product
        $shop = Shop::inRandomOrder()->first();
        $product = Product::inRandomOrder()->first();
        
        // Generate a random date (e.g., within the last 90 days)
        $date = fake()->dateTimeBetween('-90 days', 'now');
        $carbonDate = Carbon::instance($date);
        
        // Get the product price
        $price = $product->price;
        
        // Generate random units_sold between 35 and 185
        $unitsSold = fake()->numberBetween(35, 185);
        
        // Calculate day_of_week (0 = Sunday, 6 = Saturday)
        $dayOfWeek = $carbonDate->dayOfWeek;
        
        // Check if weekend (Saturday or Sunday)
        $isWeekend = in_array($dayOfWeek, [0, 6]);
        
        // Random holiday flag (you can customize this logic)
        $isHoliday = fake()->boolean(10); // 10% chance of being a holiday
        
        // Random promotion flag
        $promotionFlag = fake()->boolean(30); // 30% chance of promotion
        
        // Calculate lag_7_units_sold (units sold 7 days ago for same shop and product)
        $sevenDaysAgo = (clone $carbonDate)->subDays(7);
        $lag7UnitsSold = Sale::where('shop_id', $shop->id)
            ->where('product_id', $product->id)
            ->whereDate('date', $sevenDaysAgo->format('Y-m-d'))
            ->count(); // Count how many sales happened 7 days ago
        
        // If no sales 7 days ago, use a random baseline
        if ($lag7UnitsSold === 0) {
            $lag7UnitsSold = fake()->numberBetween(0, 50);
        }
        
        return [
            'date' => $carbonDate->format('Y-m-d'),
            'shop_id' => $shop->id,
            'product_id' => $product->id,
            'units_sold' => $unitsSold,
            'price' => $price,
            'promotion_flag' => $promotionFlag,
            'day_of_week' => $dayOfWeek,
            'is_weekend' => $isWeekend,
            'is_holiday' => $isHoliday,
            'lag_7_units_sold' => $lag7UnitsSold,
        ];
    }
    
    /**
     * Create sale with specific date.
     */
    public function forDate($date): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => $date,
        ]);
    }
    
    /**
     * Create sale with promotion.
     */
    public function withPromotion(): static
    {
        return $this->state(fn (array $attributes) => [
            'promotion_flag' => true,
        ]);
    }
    
    /**
     * Create sale without promotion.
     */
    public function withoutPromotion(): static
    {
        return $this->state(fn (array $attributes) => [
            'promotion_flag' => false,
        ]);
    }
    
    /**
     * Create sale on weekend.
     */
    public function onWeekend(): static
    {
        return $this->state(function (array $attributes) {
            $date = Carbon::parse($attributes['date'] ?? now());
            // Adjust to next Saturday
            if ($date->dayOfWeek !== 6) {
                $date->next(Carbon::SATURDAY);
            }
            
            return [
                'date' => $date->format('Y-m-d'),
                'day_of_week' => $date->dayOfWeek,
                'is_weekend' => true,
            ];
        });
    }
    
    /**
     * Create sale on holiday.
     */
    public function onHoliday(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_holiday' => true,
        ]);
    }
}
