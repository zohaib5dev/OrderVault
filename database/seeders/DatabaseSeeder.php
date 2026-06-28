<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use App\Models\Vendor;
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
        // ============================================
        // 1. SEED PLANS
        // ============================================
        $this->command->info('Seeding plans...');
        
        $plans = [
            [
                'name' => 'One Time Payment',
                'price' => 29.99,
                'type' => "one_time",
                'price_id' => 'price_basic_001',
                'description' => 'Basic plan for small businesses. Includes essential features to get started.',
                'is_active' => true,
            ],
        
            [
                'name' => 'Monthly Subscription',
                'price' => 5,
                'type' => "subscription",
                'price_id' => 'price_1TiedlAtlausncUWIzLSVQqd',
                'description' => 'Premium plan for high-volume businesses. Includes all features, API access, and 24/7 support.',
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
        
        $this->command->info('✓ Plans seeded: ' . count($plans));

        // ============================================
        // 3. SEED VENDORS WITH USERS
        // ============================================
        $this->command->info('Seeding vendors and users...');
        
        $planIds = Plan::pluck('id')->toArray();
        
        $vendors = [
            [
                'user' => [
                    'name' => 'John Smith',
                    'email' => 'admin@example.com',
                    'phone' => '+1234567890',
                    'role' => 'admin',
                ],
                'vendor' => [
                    'business_name' => 'Tech Solutions Inc.',
                    'business_address' => '123 Tech Street, Silicon Valley, CA 94025',
                    'commission_rate' => 10.00,
                ]
            ],
            [
                'user' => [
                    'name' => 'Sarah Johnson',
                    'email' => 'sarah@ecoshop.com',
                    'phone' => '+9876543210',
                    'role' => 'vendor',
                ],
                'vendor' => [
                    'business_name' => 'EcoShop',
                    'business_address' => '456 Green Avenue, Portland, OR 97201',
                    'commission_rate' => 15.00,
                ]
            ],
            [
                'user' => [
                    'name' => 'Michael Chen',
                    'email' => 'michael@fashionhub.com',
                    'phone' => '+1122334455',
                    'role' => 'vendor',
                ],
                'vendor' => [
                    'business_name' => 'Fashion Hub',
                    'business_address' => '789 Style Blvd, New York, NY 10001',
                    'commission_rate' => 12.50,
                ]
            ],
            
        ];

        foreach ($vendors as $vendorData) {
            // Create user
            $user = User::create([
                'name' => $vendorData['user']['name'],
                'email' => $vendorData['user']['email'],
                'phone' => $vendorData['user']['phone'],
                'password' => Hash::make('password'),
                'role' => $vendorData['user']['role'],
                'email_verified_at' => now(),
                'last_login_at' => now(),
                'last_login_ip' => '127.0.0.1',
            ]);

            // Create vendor with random plan
            $planId = $planIds[array_rand($planIds)];
            
            Vendor::create([
                'user_id' => $user->id,
                'business_name' => $vendorData['vendor']['business_name'],
                'business_address' => $vendorData['vendor']['business_address'],
                'plan_id' => $planId,
                'commission_rate' => $vendorData['vendor']['commission_rate'],
                'is_active' => true,
                'business_logo' => null,
            ]);
        }

        $this->command->info('✓ Vendors seeded: ' . count($vendors));

        // ============================================
        // 4. SEED CUSTOMERS
        // ============================================
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
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'email_verified_at' => now(),
                'last_login_at' => now(),
                'last_login_ip' => '127.0.0.1',
            ]);
        }

    

        // ============================================
        // SUMMARY
        // ============================================
        $this->command->info('');
        $this->command->info('============================================');
        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->info('============================================');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('  - Plans: ' . Plan::count());
        $this->command->info('  - Users: ' . User::count());
        $this->command->info('  - Vendors: ' . Vendor::count());
        $this->command->info('  - Customers: ' . User::where('role', 'customer')->count());
        $this->command->info('  - Admins: ' . User::where('role', 'admin')->count());
        $this->command->info('');
        $this->command->info('🔑 Test Credentials:');
        $this->command->info('  - Admin: admin@example.com / password123');
        $this->command->info('  - Vendor: vendor@test.com / password123');
        $this->command->info('  - Customer: customer@test.com / password123');
        $this->command->info('');
        $this->command->info('💡 For more vendor emails, check the vendor list above.');
        $this->command->info('============================================');
    }
}