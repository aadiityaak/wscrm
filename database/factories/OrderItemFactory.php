<?php

namespace Database\Factories;

use App\Models\DomainPrice;
use App\Models\HostingPlan;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $itemTypes = ['domain', 'hosting'];
        $itemType = fake()->randomElement($itemTypes);

        $data = [
            'order_id' => Order::factory(),
            'item_type' => $itemType,
            'quantity' => fake()->numberBetween(1, 5),
        ];

        if ($itemType === 'domain') {
            // Use existing domain price or create with unique extension
            $existingDomainPrice = DomainPrice::where('is_active', true)->inRandomOrder()->first();
            if ($existingDomainPrice) {
                $data['item_id'] = $existingDomainPrice->id;
                $data['price'] = $existingDomainPrice->selling_price;
            } else {
                $domainPrice = DomainPrice::factory()->active()->create();
                $data['item_id'] = $domainPrice->id;
                $data['price'] = $domainPrice->selling_price;
            }
            $data['domain_name'] = fake()->domainName();
        } else {
            // Use existing hosting plan or create new one
            $existingHostingPlan = HostingPlan::where('is_active', true)->inRandomOrder()->first();
            if ($existingHostingPlan) {
                $data['item_id'] = $existingHostingPlan->id;
                $data['price'] = $existingHostingPlan->selling_price;
            } else {
                $hostingPlan = HostingPlan::factory()->active()->create();
                $data['item_id'] = $hostingPlan->id;
                $data['price'] = $hostingPlan->selling_price;
            }
            $data['domain_name'] = fake()->domainName();
        }

        return $data;
    }

    public function domain(): static
    {
        return $this->state(fn (array $attributes) => [
            'item_type' => 'domain',
            'domain_name' => fake()->domainName(),
            'price' => fake()->randomFloat(2, 100000, 500000),
        ]);
    }

    public function hosting(): static
    {
        return $this->state(fn (array $attributes) => [
            'item_type' => 'hosting',
            'domain_name' => fake()->domainName(),
            'price' => fake()->randomFloat(2, 200000, 2000000),
        ]);
    }
}
