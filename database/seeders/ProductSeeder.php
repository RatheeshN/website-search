<?php
namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 0; $i < 20; $i++) {
            Product::create([
                'name' => $faker->word,
                'description' => $faker->paragraph,
                'category' => $faker->randomElement(['Electronics', 'Books', 'Clothing']),
                'price' => $faker->randomFloat(2, 10, 1000),
            ]);
        }
    }
}