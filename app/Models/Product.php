<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Jobs\UpdateSearchIndex;

class Product extends Model
{
    use HasFactory, Searchable;

    protected $fillable = ['name', 'description', 'category', 'price'];
    protected static function booted()
    {
        static::created(fn($model) => UpdateSearchIndex::dispatch($model));
        static::updated(fn($model) => UpdateSearchIndex::dispatch($model));
        static::deleted(fn($model) => UpdateSearchIndex::dispatch($model));
    }
    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
        ];
    }
}