<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\ExpenseCategory;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSupplierSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Staff Users
        $users = [
            [
                'name' => 'Aditya Verma',
                'email' => 'owner@flooros.com',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'phone' => '+91 98450 11111',
                'status' => 'active',
            ],
            [
                'name' => 'Kavita Menon',
                'email' => 'manager@flooros.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'phone' => '+91 98450 22222',
                'status' => 'active',
            ],
            [
                'name' => 'Rohan Sen',
                'email' => 'sales@flooros.com',
                'password' => Hash::make('password'),
                'role' => 'sales',
                'phone' => '+91 98450 33333',
                'status' => 'active',
            ],
            [
                'name' => 'Prakash Naik',
                'email' => 'inventory@flooros.com',
                'password' => Hash::make('password'),
                'role' => 'inventory',
                'phone' => '+91 98450 44444',
                'status' => 'active',
            ],
            [
                'name' => 'Sunil Gupta',
                'email' => 'accountant@flooros.com',
                'password' => Hash::make('password'),
                'role' => 'accountant',
                'phone' => '+91 98450 55555',
                'status' => 'active',
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(['email' => $u['email']], $u);
        }

        // 2. Customers
        $customers = [
            [
                'name' => 'Rajesh Sharma',
                'company_name' => null,
                'customer_type' => 'individual',
                'phone' => '+91 98860 12345',
                'email' => 'rajesh.sharma@gmail.com',
                'address' => 'Villa 14, Prestige Silver Oak, Whitefield',
                'city' => 'Bengaluru',
                'pincode' => '560066',
                'gstin' => null,
                'opening_balance' => 0.00,
                'notes' => 'Renovating 3BHK duplex. Prefers natural engineered oak.',
            ],
            [
                'name' => 'Studio Arc Architecture & Interiors',
                'company_name' => 'Studio Arc Pvt Ltd',
                'customer_type' => 'interior_designer',
                'phone' => '+91 98441 55667',
                'email' => 'projects@studioarc.design',
                'address' => '402, 12th Main Road, HAL 2nd Stage, Indiranagar',
                'city' => 'Bengaluru',
                'pincode' => '560038',
                'gstin' => '29AABCS1234F1Z8',
                'opening_balance' => 0.00,
                'notes' => 'Regular commercial interior design studio. Net 15 credit terms.',
            ],
            [
                'name' => 'Prestige Living Infra Projects',
                'company_name' => 'Prestige Living Infrastructure LLP',
                'customer_type' => 'builder',
                'phone' => '+91 99000 88776',
                'email' => 'procurement@prestigeliving.co',
                'address' => 'Floor 6, Concorde Towers, Koramangala',
                'city' => 'Bengaluru',
                'pincode' => '560095',
                'gstin' => '29AAACP9988G1ZQ',
                'opening_balance' => 15000.00,
                'notes' => 'Contractor building boutique luxury apartments in Sarjapur.',
            ],
            [
                'name' => 'Meera Nambiar',
                'company_name' => 'Nambiar & Associates Design',
                'customer_type' => 'contractor',
                'phone' => '+91 97312 99881',
                'email' => 'meera@nambiardesign.in',
                'address' => 'Penthouse B, 7 Lavelle Road',
                'city' => 'Bengaluru',
                'pincode' => '560001',
                'gstin' => '29AAFCN5544H1ZL',
                'opening_balance' => 0.00,
                'notes' => 'Architect working on high-end residential bungalow projects.',
            ],
        ];

        foreach ($customers as $c) {
            Customer::updateOrCreate(['phone' => $c['phone']], $c);
        }

        // 3. Suppliers
        $suppliers = [
            [
                'name' => 'Mikasa Floors & Greenlam Industries',
                'company_name' => 'Greenlam Industries Limited',
                'contact_person' => 'Vikram Joshi (Regional Head)',
                'phone' => '+91 80 4123 4567',
                'email' => 'sales.south@mikasafloors.com',
                'address' => 'Makali Post, Tumkur Road',
                'city' => 'Bengaluru',
                'gstin' => '29AAACG2123M1ZV',
                'opening_balance' => 0.00,
                'notes' => 'Primary OEM supplier for European engineered wood and parquet.',
            ],
            [
                'name' => 'ShieldCore Polymer Surfaces India',
                'company_name' => 'ShieldCore Composites Pvt Ltd',
                'contact_person' => 'Arun Kumar',
                'phone' => '+91 44 2888 9900',
                'email' => 'orders@shieldcore.in',
                'address' => 'SIPCOT Industrial Park, Sriperumbudur',
                'city' => 'Chennai',
                'gstin' => '33AABCS4455N1ZX',
                'opening_balance' => 0.00,
                'notes' => 'Direct manufacturer of 100% waterproof stone plastic composite (SPC).',
            ],
            [
                'name' => 'Decoria Luxury Surfaces',
                'company_name' => 'Decoria Imports & Distribution',
                'contact_person' => 'Sameer Khan',
                'phone' => '+91 22 2677 8899',
                'email' => 'supply@decoriafloors.com',
                'address' => 'Unit 12, Peninsula Corporate Park, Lower Parel',
                'city' => 'Mumbai',
                'gstin' => '27AAACD7788K1Z5',
                'opening_balance' => 0.00,
                'notes' => 'Importer of commercial LVT and German AC5 laminate boards.',
            ],
        ];

        foreach ($suppliers as $s) {
            Supplier::updateOrCreate(['name' => $s['name']], $s);
        }

        // 4. Expense Categories
        $categories = [
            ['name' => 'Showroom Rent & Maintenance', 'description' => 'Monthly retail gallery lease and facility fees'],
            ['name' => 'Electricity & Utilities', 'description' => 'Power, water, and broadband charges'],
            ['name' => 'Showroom Staff & Installation Labor', 'description' => 'Salaries, overtime, and floor fitting labor wages'],
            ['name' => 'Freight, Transport & Loading', 'description' => 'Warehouse haulage, local tempo transport, and handling'],
            ['name' => 'Showroom Tools & Installation Supplies', 'description' => 'Underlay rolls, adhesives, skirting clips, spacers, saws'],
            ['name' => 'Digital Marketing & Catalog Printing', 'description' => 'Architect catalog lookbooks, sample swatches, local ads'],
            ['name' => 'Office Stationery & Miscellaneous', 'description' => 'Tea, coffee, invoice stationery, general repairs'],
        ];

        foreach ($categories as $ec) {
            ExpenseCategory::updateOrCreate(['name' => $ec['name']], $ec);
        }
    }
}
