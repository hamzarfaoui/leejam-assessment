<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    // Declare a protected property to hold the authenticated user.
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user for authentication.
        $this->user = User::factory()->create();

        // Authenticate the test user.
        $this->actingAs($this->user, 'api');
    }

    /** @test */
    public function it_can_list_all_tasks()
    {
        // Create 5 tasks for testing.
        Task::factory(5)->create();

        // Send a GET request to list tasks.
        $response = $this->get('/api/tasks');

        // Assert response status and JSON structure.
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'due_date',
                        'status',
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_can_create_a_task()
    {
        // Task data to be created.
        $taskData = [
            'title' => 'New Task',
            'description' => 'Task Description',
            'due_date' => '2023-12-31',
            'status' => 'pending',
        ];

        // Send a POST request to create a task.
        $response = $this->post('/api/tasks', $taskData);

        // Assert response status and JSON structure.
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'due_date',
                    'status',
                ],
            ]);
    }

    /** @test */
    public function it_can_show_a_task()
    {
        // Create a task for testing.
        $task = Task::factory()->create();

        // Send a GET request to show a specific task.
        $response = $this->get("/api/tasks/{$task->id}");

        // Assert response status and JSON structure.
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'due_date',
                    'status',
                ],
            ]);
    }

    /** @test */
    public function it_can_update_a_task()
    {
        // Create a task for testing.
        $task = Task::factory()->create();
        
        // Updated task data.
        $updatedData = [
            'title' => 'Updated Task Title',
            'description' => 'Updated Task Description',
            'due_date' => '2023-12-31',
            'status' => 'completed',
        ];

        // Send a PUT request to update the task.
        $response = $this->put("/api/tasks/{$task->id}", $updatedData);

        // Assert response status and JSON data.
        $response->assertStatus(200)
            ->assertJson([
                'data' => $updatedData,
            ]);
    }

    /** @test */
    public function it_can_delete_a_task()
    {
        // Create a task for testing.
        $task = Task::factory()->create();

        // Send a DELETE request to delete the task.
        $response = $this->delete("/api/tasks/{$task->id}");

        // Assert response status and check if the task is deleted from the database.
        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
