<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BlogPost;
use App\Models\Product;
use App\Models\Page;
use App\Models\Faq;

class RebuildSearchIndex extends Command
{
    protected $signature = 'scout:rebuild';
    protected $description = 'Rebuild the search index for all searchable models';

    public function handle(): void
    {
        $this->info('Rebuilding search index...');

        $models = [
            BlogPost::class,
            Product::class,
            Page::class,
            Faq::class,
        ];

        foreach ($models as $model) {
            $this->info("Flushing and indexing {$model}...");
            $this->call('scout:flush', ['model' => $model]);
            $this->call('scout:import', ['model' => $model]);
            $this->info("{$model} indexed successfully.");
        }

        $this->info('Search index rebuilt successfully.');
    }
}