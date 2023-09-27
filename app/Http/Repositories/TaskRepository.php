<?php

namespace App\Http\Repositories;

use App\Http\Repositories\Interfaces\TaskRepositoryInterface;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;

class TaskRepository implements TaskRepositoryInterface
{
    public function allTasks()
    {
        // Cache the list of all tasks for 30 minutes if not in cache.
        $tasks = Cache::remember('all-tasks', now()->addMinutes(30), function () {
            // Retrieve tasks with user relationships and paginate them (9 tasks per page).
            $data = Task::with('user') 
                        ->paginate(9);
            return TaskResource::collection($data);
        });

        return $tasks; // Return the cached tasks.
    }

    public function findTask($data)
    {
        // Cache the specific task for 30 minutes if not in cache.
        $task = Cache::remember("task-{$data->id}", now()->addMinutes(30), function () use ($data) {
            return new TaskResource($data);
        });

        return $task; // Return the cached task.
    }

    public function saveTask($data)
    {
        // Create a new task with the request data.
        $task = Task::create($data);

        // Return a JSON response with the newly created task.
        return new TaskResource($task);
    }

    public function updateTask($data, $task)
    {
        // Get all request data.
        $requestData = $data->all();

        // Fill the task with the updated data.
        $task = $task->fill($requestData);

        // Save the updated task.
        $task->save();

        // Return a JSON response with the updated task.
        return new TaskResource($task);
    }

    public function deleteTask($data)
    {
        // Delete the specified task.
        $data->delete();

        // Return a JSON response indicating success (HTTP 204 No Content).
        return response(null,204);
    }
}
