<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        parent::booted();

        // Event listener for updating a Task
        static::updating(function (Task $task) {
            // Remove cache for the specific task
            Cache::forget("task-{$task->id}");

            // Remove cache for all tasks (this caches a list of all tasks)
            Cache::forget("all-tasks");
        });

        // Event listener for deleting a Task
        static::deleting(function (Task $task) {
            // Remove cache for the specific task
            Cache::forget("task-{$task->id}");

            // Remove cache for all tasks (this caches a list of all tasks)
            Cache::forget("all-tasks");
        });

        // Event listener for creating a new Task
        static::created(function () {
            // Remove cache for all tasks (this caches a list of all tasks)
            Cache::forget("all-tasks");
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
