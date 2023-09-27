<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Repositories\TaskRepository;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    private $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;

        // Middleware to authenticate API requests.
        $this->middleware('auth:api');

        // Middleware to throttle API requests to prevent abuse (60 requests per minute).
        $this->middleware('throttle:60,1');

        // Authorization for resource methods (index, store, show, update, destroy).
        // The 'task' parameter refers to the Task model.
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Retrieve all tasks.
     *
     * @return \Illuminate\Http\Response
     *
     * @OA\Get(
     *     path="/api/tasks",
     *     tags={"Tasks"},
     *     summary="Get all tasks",
     *     operationId="index",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *             type="string"
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/xml",
     *             @OA\Schema(
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index()
    {
        // Retrieve and return all tasks as JSON.
         return $this->taskRepository->allTasks();
    }

    /**
     * Create a new task.
     *
     * @param  \App\Http\Requests\StoreTaskRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @OA\Post(
     *     path="/api/tasks",
     *     tags={"Tasks"},
     *     summary="Create a new task",
     *     operationId="store",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string", example="Task Title"),
     *             @OA\Property(property="description", type="string", example="Task Description"),
     *             @OA\Property(property="due_date", type="string", format="date", example="2023-12-31"),
     *             @OA\Property(property="status", type="string", enum={"pending", "completed"}, example="pending")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", format="int64", example=1),
     *             @OA\Property(property="title", type="string", example="Task Title"),
     *             @OA\Property(property="description", type="string", example="Task Description"),
     *             @OA\Property(property="due_date", type="string", format="date", example="2023-12-31"),
     *             @OA\Property(property="status", type="string", enum={"pending", "completed"}, example="pending"),
     *             @OA\Property(property="user_id", type="integer", format="int64", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity (Validation Error)"
     *     )
     * )
     */
    public function store(StoreTaskRequest $request)
    {
        // Get all request data.
        $requestData = $request->all();

        // Add the authenticated user's ID to the request data.
        $requestData['user_id'] = Auth::id();

        // Save the task and return the JSON response.
        return $this->taskRepository->saveTask($requestData);
    }

    /**
     * Retrieve a specific task by ID.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     *
     * @OA\Get(
     *     path="/api/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Retrieve a specific task by ID",
     *     operationId="show",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the task to retrieve",
     *         @OA\Schema(type="integer", format="int64", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", format="int64", example=1),
     *             @OA\Property(property="title", type="string", example="Task Title"),
     *             @OA\Property(property="description", type="string", example="Task Description"),
     *             @OA\Property(property="due_date", type="string", format="date", example="2023-12-31"),
     *             @OA\Property(property="status", type="string", enum={"pending", "completed"}, example="pending"),
     *             @OA\Property(property="user_id", type="integer", format="int64", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     )
     * )
     */
    public function show(Task $task)
    {
        // Retrieve and return the task as JSON.
        return  $this->taskRepository->findTask($task);
    }

    /**
     * Update a specific task by ID.
     *
     * @param  \App\Http\Requests\UpdateTaskRequest  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     *
     * @OA\Put(
     *     path="/api/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Update a specific task by ID",
     *     operationId="update",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the task to update",
     *         @OA\Schema(type="integer", format="int64", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string", maxLength=255, example="Updated Task Title"),
     *             @OA\Property(property="due_date", type="string", format="date", example="2023-12-31"),
     *             @OA\Property(property="description", type="string", example="Updated Task Description"),
     *             @OA\Property(property="status", type="string", enum={"pending", "in progress", "completed"}, example="completed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", format="int64", example=1),
     *             @OA\Property(property="title", type="string", example="Updated Task Title"),
     *             @OA\Property(property="description", type="string", example="Updated Task Description"),
     *             @OA\Property(property="due_date", type="string", format="date", example="2023-12-31"),
     *             @OA\Property(property="status", type="string", enum={"pending", "in progress", "completed"}, example="completed"),
     *             @OA\Property(property="user_id", type="integer", format="int64", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden. You don't have permission to update this task."
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity (Validation Error)"
     *     )
     * )
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        // Update the task and return the JSON response.
        return $this->taskRepository->updateTask($request, $task);
    }

    /**
     * Delete a specific task by ID.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     *
     * @OA\Delete(
     *     path="/api/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Delete a specific task by ID",
     *     operationId="destroy",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the task to delete",
     *         @OA\Schema(type="integer", format="int64", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Task deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden. You don't have permission to delete this task."
     *     )
     * )
     */
    public function destroy(Task $task)
    {
        // Delete the task and return the JSON response.
        return  $this->taskRepository->deleteTask($task);
    }
}
