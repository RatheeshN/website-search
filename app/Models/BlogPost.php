<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Jobs\UpdateSearchIndex;

class BlogPost extends Model
{
    use HasFactory, Searchable;

    protected $fillable = ['title', 'body', 'tags', 'published_at'];

    protected $casts = [
        'published_at' => 'datetime',
        'tags' => 'array',
    ];
    protected static function booted()
    {
        static::created(fn($model) => UpdateSearchIndex::dispatch($model));
        static::updated(fn($model) => UpdateSearchIndex::dispatch($model));
        static::deleted(fn($model) => UpdateSearchIndex::dispatch($model));
    }
    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'tags' => implode(' ', is_array($this->tags) ? $this->tags : []),
        ];
    }
}
