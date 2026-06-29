<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'phone' => '123123123',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'last_login_at' => now(),
            'last_login_ip' => '127.0.0.1',
        ]);

        $this->command->info('Seeding customers...');

        $customers = [
            [
                'name' => 'Alice Wonderland',
                'email' => 'alice@example.com',
                'phone' => '+1112223333',
                'address' => '+1112223333',
            ],
            [
                'name' => 'Bob Marley',
                'email' => 'bob@example.com',
                'phone' => '+2223334444',
                'address' => '+1112223333',
            ],
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie@example.com',
                'phone' => '+3334445555',
            ],
            [
                'name' => 'Diana Prince',
                'email' => 'diana@example.com',
                'phone' => '+4445556666',
            ],
            [
                'name' => 'Eve Adams',
                'email' => 'eve@example.com',
                'phone' => '+5556667777',
            ],
            [
                'name' => 'Frank Castle',
                'email' => 'frank@example.com',
                'phone' => '+6667778888',
            ],
            [
                'name' => 'Grace Hopper',
                'email' => 'grace@example.com',
                'phone' => '+7778889999',
            ],
            [
                'name' => 'Henry Ford',
                'email' => 'henry@example.com',
                'phone' => '+8889990000',
            ],
            [
                'name' => 'Ivy League',
                'email' => 'ivy@example.com',
                'phone' => '+9990001111',
            ],
            [
                'name' => 'Jack Sparrow',
                'email' => 'jack@example.com',
                'phone' => '+0001112222',
            ],
        ];

        foreach ($customers as $customer) {
            User::create([
                'name' => $customer['name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
                'password' => Hash::make('password'),
                'role' => 'customer',
                'email_verified_at' => now(),
                'last_login_at' => now(),
                'last_login_ip' => '127.0.0.1',
            ]);
        }
    }
}
