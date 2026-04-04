<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        $manager = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => bcrypt('password'),
        ]);
        $manager->assignRole($managerRole);

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole($adminRole);

        $customers = Customer::factory(5)->create();

        $customers->each(function (Customer $customer) {
            Ticket::factory(rand(2, 5))->create([
                'customer_id' => $customer->id,
            ]);
        });
    }
}
