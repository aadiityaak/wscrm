<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'phone' => '+62812345678',
            'address' => 'Jl. Test Address No. 123',
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'postal_code' => '12345',
            'status' => 'active',
        ]);

        Customer::create([
            'name' => 'John Doe',
            'email' => 'john@customer.com',
            'password' => Hash::make('password'),
            'phone' => '+62856789012',
            'address' => 'Jl. Sample Street No. 456',
            'city' => 'Bandung',
            'country' => 'Indonesia',
            'postal_code' => '54321',
            'status' => 'active',
        ]);
    }
}
