<?php

namespace Database\Factories;

use App\Models\ServicePlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServicePlanFactory extends Factory
{
    protected $model = ServicePlan::class;

    public function definition(): array
    {
        $planNames = [
            'Website Maintenance', 'SEO Optimization', 'Content Management',
            'Security Monitoring', 'Backup Service', 'Performance Optimization',
            'Domain Management', 'Email Hosting', 'Database Administration',
        ];

        $planName = fake()->randomElement($planNames);
        $baseCost = fake()->randomFloat(2, 100000, 1000000); // IDR 100k-1M
        $markup = fake()->randomFloat(2, 1.3, 2.5); // 30-150% markup

        return [
            'plan_name' => $planName,
            'description' => fake()->sentence(10, true),
            'base_cost' => $baseCost,
            'selling_price' => $baseCost * $markup,
            'features' => $this->generateFeatures(),
            'is_active' => fake()->boolean(85), // 85% chance active
        ];
    }

    private function generateFeatures(): array
    {
        $allFeatures = [
            'Monthly Reports',
            '24/7 Support',
            'Backup Management',
            'Security Scanning',
            'Performance Monitoring',
            'Content Updates',
            'Plugin Updates',
            'Theme Customization',
            'SEO Optimization',
            'Analytics Setup',
            'Social Media Integration',
            'Email Configuration',
        ];

        return fake()->randomElements($allFeatures, fake()->numberBetween(3, 8));
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan_name' => 'Website Maintenance',
            'description' => 'Complete website maintenance and support service.',
        ]);
    }
}
