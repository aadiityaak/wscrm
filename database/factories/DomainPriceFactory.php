<?php

namespace Database\Factories;

use App\Models\DomainPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

class DomainPriceFactory extends Factory
{
    protected $model = DomainPrice::class;

    public function definition(): array
    {
        $extensions = ['.com', '.net', '.org', '.info', '.biz', '.co.id', '.web.id', '.my.id', '.id'];
        $extension = fake()->randomElement($extensions);

        $baseCost = fake()->randomFloat(2, 50000, 200000); // IDR 50k-200k
        $renewalCost = $baseCost * fake()->randomFloat(2, 0.8, 1.2); // ±20% variance
        $markup = fake()->randomFloat(2, 1.3, 2.0); // 30-100% markup

        $sellingPrice = $baseCost * $markup;
        $renewalPriceWithTax = $renewalCost * 1.11; // +11% tax

        return [
            'extension' => $extension,
            'base_cost' => $baseCost,
            'renewal_cost' => $renewalCost,
            'selling_price' => $sellingPrice,
            'renewal_price_with_tax' => $renewalPriceWithTax,
            'is_active' => fake()->boolean(85), // 85% chance active
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'extension' => fake()->randomElement(['.com', '.net', '.org']),
            'is_active' => true,
        ]);
    }
}
