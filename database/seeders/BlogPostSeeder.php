<?php
namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 0; $i < 20; $i++) {
            BlogPost::create([
                'title' => $faker->sentence,
                'body' => $faker->paragraphs(3, true),
                'tags' => $faker->words(3),
                'published_at' => $faker->dateTimeThisYear(),
            ]);
        }
    }
}