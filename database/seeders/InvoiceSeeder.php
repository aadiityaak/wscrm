<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing orders and customers
        $orders = Order::all();
        $customers = Customer::all();

        if ($orders->isEmpty()) {
            $this->command->warn('No orders found. Please run OrderSeeder first.');

            return;
        }

        if ($customers->isEmpty()) {
            $this->command->warn('No customers found. Please run CustomerSeeder first.');

            return;
        }

        // Create invoices for existing orders
        $orders->each(function ($order) {
            // 80% chance to create an invoice for each order
            if (fake()->boolean(80)) {
                Invoice::factory()
                    ->for($order->customer)
                    ->for($order)
                    ->create([
                        'amount' => $order->total_amount,
                    ]);
            }
        });

        // Create some standalone invoices (services, renewals, etc.)
        $customers->each(function ($customer) {
            // 30% chance to create additional invoices for services
            if (fake()->boolean(30)) {
                $invoiceCount = fake()->numberBetween(1, 2);

                for ($i = 0; $i < $invoiceCount; $i++) {
                    Invoice::factory()
                        ->for($customer)
                        ->create([
                            'order_id' => null, // Service or renewal invoice
                        ]);
                }
            }
        });

        $this->command->info('Invoices created successfully!');
    }
}
