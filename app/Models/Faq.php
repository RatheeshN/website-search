<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Jobs\UpdateSearchIndex;

class Faq extends Model
{
    use HasFactory, Searchable;

    protected $fillable = ['question', 'answer'];
    protected static function booted()
    {
        static::created(fn($model) => UpdateSearchIndex::dispatch($model));
        static::updated(fn($model) => UpdateSearchIndex::dispatch($model));
        static::deleted(fn($model) => UpdateSearchIndex::dispatch($model));
    }
    public function toSearchableArray()
    {
        return [
            'question' => $this->question,
            'answer' => $this->answer,
        ];
    }
}