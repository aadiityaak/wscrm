<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\HostingPlan;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $serviceTypes = ['hosting', 'domain'];
        $statuses = ['active', 'suspended', 'terminated', 'pending'];

        $serviceType = fake()->randomElement($serviceTypes);
        $domainName = fake()->domainName();

        return [
            'customer_id' => Customer::factory(),
            'service_type' => $serviceType,
            'domain_name' => $domainName,
            'status' => fake()->randomElement($statuses),
            'plan_id' => $serviceType === 'hosting' ? HostingPlan::factory() : null,
            'expires_at' => fake()->dateTimeBetween('now', '+2 years'),
            'auto_renew' => fake()->boolean(70), // 70% chance of auto-renew
        ];
    }

    public function hosting(): static
    {
        return $this->state(fn (array $attributes) => [
            'service_type' => 'hosting',
            'plan_id' => HostingPlan::factory(),
        ]);
    }

    public function domain(): static
    {
        return $this->state(fn (array $attributes) => [
            'service_type' => 'domain',
            'plan_id' => null,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}
