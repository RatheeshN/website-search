<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BlogPostSeeder::class,
            ProductSeeder::class,
            PageSeeder::class,
            FaqSeeder::class,
        ]);
    }
}