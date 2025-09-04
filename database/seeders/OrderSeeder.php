<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing customers
        $customers = Customer::all();

        if ($customers->isEmpty()) {
            $this->command->warn('No customers found. Please run CustomerSeeder first.');

            return;
        }

        // Create orders for existing customers
        $customers->each(function ($customer) {
            // Create 1-3 orders per customer
            $orderCount = fake()->numberBetween(1, 3);

            for ($i = 0; $i < $orderCount; $i++) {
                $order = Order::factory()
                    ->forCustomer($customer)
                    ->create();

                // Add 1-5 items per order
                $itemCount = fake()->numberBetween(1, 5);

                for ($j = 0; $j < $itemCount; $j++) {
                    OrderItem::factory()
                        ->for($order)
                        ->create();
                }
            }
        });

        $this->command->info('Orders and OrderItems created successfully!');
    }
}
