<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\HostingPlan;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing customers and hosting plans
        $customers = Customer::all();
        $hostingPlans = HostingPlan::where('is_active', true)->get();

        if ($customers->isEmpty()) {
            $this->command->warn('No customers found. Please run CustomerSeeder first.');

            return;
        }

        if ($hostingPlans->isEmpty()) {
            $this->command->warn('No active hosting plans found. Please run HostingPlanSeeder first.');

            return;
        }

        // Create services for existing customers
        $customers->each(function ($customer) use ($hostingPlans) {
            // Create 1-4 services per customer
            $serviceCount = fake()->numberBetween(1, 4);

            for ($i = 0; $i < $serviceCount; $i++) {
                $serviceType = fake()->randomElement(['hosting', 'domain']);

                if ($serviceType === 'hosting') {
                    Service::factory()
                        ->hosting()
                        ->for($customer)
                        ->create([
                            'plan_id' => $hostingPlans->random()->id,
                        ]);
                } else {
                    Service::factory()
                        ->domain()
                        ->for($customer)
                        ->create();
                }
            }
        });

        $this->command->info('Services created successfully!');
    }
}
