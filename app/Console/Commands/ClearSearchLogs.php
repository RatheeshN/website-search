<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SearchLog;

class ClearSearchLogs extends Command
{
    protected $signature = 'search:logs:clear';
    protected $description = 'Clear all search logs from the database';

    public function handle(): void
    {
        $this->info('Clearing search logs...');

        $count = SearchLog::count();
        SearchLog::truncate();

        $this->info("Cleared {$count} search logs successfully.");
    }
}