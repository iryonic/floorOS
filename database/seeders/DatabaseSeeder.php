<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        GenerateSampleTextures::generate();

        $this->call([
            SettingsSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            CustomerSupplierSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
