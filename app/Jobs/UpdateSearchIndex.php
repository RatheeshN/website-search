<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Scout\Searchable;

class UpdateSearchIndex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function handle(): void
    {
        if (in_array(Searchable::class, class_uses($this->model))) {
            $this->model->searchable();
        }
    }
}