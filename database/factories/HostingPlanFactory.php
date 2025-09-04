<?php

namespace Database\Factories;

use App\Models\HostingPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class HostingPlanFactory extends Factory
{
    protected $model = HostingPlan::class;

    public function definition(): array
    {
        $planTypes = ['Shared', 'VPS', 'Dedicated', 'Cloud'];
        $planTiers = ['Starter', 'Basic', 'Professional', 'Enterprise', 'Ultimate'];

        $planType = fake()->randomElement($planTypes);
        $planTier = fake()->randomElement($planTiers);
        $planName = $planType.' '.$planTier;

        // Generate realistic specs based on plan type
        $specs = $this->generateSpecs($planType);

        $modalCost = fake()->randomFloat(2, $specs['cost_min'], $specs['cost_max']);
        $maintenanceCost = $modalCost * fake()->randomFloat(2, 0.1, 0.3); // 10-30% of modal cost
        $discountPercent = fake()->randomFloat(2, 0, 25); // 0-25% discount
        $markup = fake()->randomFloat(2, 1.5, 3.0); // 50-200% markup

        $sellingPrice = ($modalCost * $markup) * (1 - $discountPercent / 100);

        return [
            'plan_name' => $planName,
            'storage_gb' => $specs['storage'],
            'cpu_cores' => $specs['cpu'],
            'ram_gb' => $specs['ram'],
            'bandwidth' => $specs['bandwidth'],
            'modal_cost' => $modalCost,
            'maintenance_cost' => $maintenanceCost,
            'discount_percent' => $discountPercent,
            'selling_price' => $sellingPrice,
            'features' => $this->generateFeatures($planType, $planTier),
            'is_active' => fake()->boolean(90), // 90% chance active
        ];
    }

    private function generateSpecs(string $planType): array
    {
        switch ($planType) {
            case 'Shared':
                return [
                    'storage' => fake()->randomFloat(2, 1, 20),
                    'cpu' => fake()->randomFloat(2, 0.5, 2),
                    'ram' => fake()->randomFloat(2, 0.5, 4),
                    'bandwidth' => fake()->randomElement(['Unlimited', '100GB', '500GB', '1TB']),
                    'cost_min' => 50000,
                    'cost_max' => 300000,
                ];
            case 'VPS':
                return [
                    'storage' => fake()->randomFloat(2, 20, 500),
                    'cpu' => fake()->randomFloat(2, 1, 8),
                    'ram' => fake()->randomFloat(2, 1, 32),
                    'bandwidth' => fake()->randomElement(['1TB', '2TB', '5TB', 'Unlimited']),
                    'cost_min' => 200000,
                    'cost_max' => 1500000,
                ];
            case 'Dedicated':
                return [
                    'storage' => fake()->randomFloat(2, 500, 2000),
                    'cpu' => fake()->randomFloat(2, 4, 32),
                    'ram' => fake()->randomFloat(2, 8, 128),
                    'bandwidth' => fake()->randomElement(['5TB', '10TB', '20TB', 'Unlimited']),
                    'cost_min' => 1000000,
                    'cost_max' => 10000000,
                ];
            default: // Cloud
                return [
                    'storage' => fake()->randomFloat(2, 50, 1000),
                    'cpu' => fake()->randomFloat(2, 2, 16),
                    'ram' => fake()->randomFloat(2, 4, 64),
                    'bandwidth' => 'Unlimited',
                    'cost_min' => 300000,
                    'cost_max' => 3000000,
                ];
        }
    }

    private function generateFeatures(string $planType, string $planTier): array
    {
        $baseFeatures = [
            'Free SSL Certificate',
            'Daily Backup',
            'Email Support',
            'cPanel Access',
        ];

        $advancedFeatures = [
            '24/7 Phone Support',
            'Premium Themes',
            'Advanced Security',
            'CDN Integration',
            'Database Optimization',
            'Staging Environment',
        ];

        $enterpriseFeatures = [
            'Dedicated Support Manager',
            'Custom Integration',
            'Priority Support',
            'White-label Options',
            'API Access',
        ];

        $features = $baseFeatures;

        if (in_array($planTier, ['Professional', 'Enterprise', 'Ultimate'])) {
            $features = array_merge($features, fake()->randomElements($advancedFeatures, fake()->numberBetween(1, 3)));
        }

        if (in_array($planTier, ['Enterprise', 'Ultimate'])) {
            $features = array_merge($features, fake()->randomElements($enterpriseFeatures, fake()->numberBetween(1, 2)));
        }

        return $features;
    }

    public function shared(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan_name' => 'Shared '.fake()->randomElement(['Starter', 'Basic', 'Professional']),
        ]);
    }

    public function vps(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan_name' => 'VPS '.fake()->randomElement(['Basic', 'Professional', 'Enterprise']),
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}
