<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Product;
use App\Models\SearchLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search(SearchRequest $request)
    {
        $query = $request->input('q');
        $page = $request->input('page', 1);

        if (Auth::check()) {
            SearchLog::create([
                'query' => $query,
                'user_id' => Auth::id(),
            ]);
        }

        $results = [];
        $models = [
            BlogPost::class => ['title', 'body', 'tags'],
            Product::class => ['name', 'description', 'category'],
            Page::class => ['title', 'content'],
            Faq::class => ['question', 'answer'],
        ];

        foreach ($models as $model => $fields) {
            $searchResults = $model::search($query)->get()->map(function ($item) use ($model) {
                return [
                    'type' => class_basename($model),
                    'title' => $item->title ?? $item->name ?? $item->question,
                    'snippet' => $this->getSnippet($item),
                    'link' => $this->getLink($item, class_basename($model)),
                ];
            });
            $results = array_merge($results, $searchResults->toArray());
        }

        usort($results, fn($a, $b) => strcmp($b['title'], $a['title']));

        $perPage = 10;
        $total = count($results);
        $results = array_slice($results, ($page - 1) * $perPage, $perPage);

        return response()->json([
            'results' => $results,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
            ],
        ]);
    }

    public function suggestions(Request $request)
    {
        $query = $request->input('q');
        $suggestions = [];

        $models = [BlogPost::class, Product::class, Page::class, Faq::class];
        foreach ($models as $model) {
            $suggestions = array_merge(
                $suggestions,
                $model::search($query)->take(5)->get()->pluck('title')->toArray()
            );
        }

        return response()->json(array_unique($suggestions));
    }

    public function logs(Request $request)
    {
        $this->middleware('admin');
        $logs = SearchLog::select('query')
            ->groupBy('query')
            ->orderByRaw('COUNT(*) DESC')
            ->take(10)
            ->get()
            ->pluck('query');

        return response()->json(['top_searches' => $logs]);
    }

    private function getSnippet($item)
    {
        return substr($item->body ?? $item->description ?? $item->content ?? $item->answer, 0, 100) . '...';
    }

    private function getLink($item, $type)
    {
        return match ($type) {
            'BlogPost' => "/blog/{$item->id}",
            'Product' => "/product/{$item->id}",
            'Page' => "/page/{$item->id}",
            'Faq' => "/faq/{$item->id}",
            default => '#',
        };
    }
}