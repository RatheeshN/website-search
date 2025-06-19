<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SearchLog;

class ShowTopSearchTerms extends Command
{
    protected $signature = 'search:terms:top {--limit=10 : Number of top terms to display}';
    protected $description = 'Display the top search terms with their frequency';

    public function handle(): void
    {
        $limit = $this->option('limit');
        $this->info("Fetching top {$limit} search terms...");

        $terms = SearchLog::select('query')
            ->groupBy('query')
            ->orderByRaw('COUNT(*) DESC')
            ->take($limit)
            ->get()
            ->map(function ($log, $index) {
                return [
                    'Rank' => $index + 1,
                    'Query' => $log->query,
                    'Count' => SearchLog::where('query', $log->query)->count(),
                ];
            });

        $this->table(
            ['Rank', 'Query', 'Count'],
            $terms->toArray()
        );
    }
}