<?php
namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 0; $i < 10; $i++) {
            Page::create([
                'title' => $faker->sentence,
                'content' => $faker->paragraphs(3, true),
            ]);
        }
    }
}