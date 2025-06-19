<?php
namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 0; $i < 15; $i++) {
            Faq::create([
                'question' => $faker->sentence,
                'answer' => $faker->paragraph,
            ]);
        }
    }
}