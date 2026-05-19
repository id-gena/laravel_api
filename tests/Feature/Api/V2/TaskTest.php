<?php

namespace Tests\Feature\Api\V2;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Task;
use App\Models\User;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_list_of_tasks(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        // Arrange
        $tasks = Task::factory()->count(2)->create([
            'user_id' => $user->id
        ]);
        // Act: make a GET request to the endpoint
        $response = $this->getJson('/api/v2/tasks');
        // Assert: status is OK and data has 2 items
        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure([
            'data' => [
                ['id', 'name', 'is_completed']
            ],
        ]);
    }

    // Check if user can get a single task
    public function test_user_can_get_single_task(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        // Arrange: create a task
        $task = Task::factory()->create([
            'user_id' => $user->id
        ]);
        // Act: make a GET request to the endpoint with task ID
        $response = $this->getJson('/api/v2/tasks/' . $task->id);
        // Assert: response contains the correct data.
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'is_completed']
        ]);
        $response->assertJson([
            'data' => [
                'id' => $task->id,
                'name' => $task->name,
                'is_completed' => $task->is_completed,
            ]
        ]);
    }

    // POST tasks - create a new task
    public function test_user_can_create_a_task(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->postJson('/api/v2/tasks', [
            'name' => 'New task'
        ]);

        $response->assertCreated();
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'is_completed']
        ]);
        $this->assertDatabaseHas('tasks', [
            'name' => 'New task'
        ]);
    }

    // Ensure user cannot create invalid task
    public function test_user_cannot_create_invalid_task(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->postJson('/api/v2/tasks', [
            'name' => '',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    // Update existing task
    // @todo Resolve the issue when the test is not passed after Auth implementation
    public function test_user_can_update_task(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $task = Task::factory()->create();

        $response = $this->putJson('/api/v2/tasks/' . $task->id, [
            'name' => 'Updated task name'
        ]);

        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'Updated task name'
        ]);
    }

    // User cannot update task with invalid data
    public function test_user_cannot_update_task_with_invalid_data(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $task = Task::factory()->create();

        $response = $this->putJson('/api/v2/tasks/' . $task->id, [
            'name' => ''
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    // User can switch the task as completed or incompleted
    // @todo Resolve the issue when the test is not passed after Auth implementation
    public function test_user_can_toggle_task_completion(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $task = Task::factory()->create([
            'is_completed' => false
        ]);

        $response = $this->patchJson('/api/v2/tasks/' . $task->id . '/complete', [
            'is_completed' => true
        ]);

        $response->assertOk();
        $response->assertJsonFragment([
            'is_completed' => true
        ]);
    }

    // User cannot toggle is_completed status with invalid data
    public function test_user_cannot_toggle_completed_with_invalid_data(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $task = Task::factory()->create();

        $response = $this->patchJson('/api/v2/tasks/' . $task->id . '/complete', [
            'is_completed' => 'yes'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['is_completed']);
    }

    // User can delete a task
    // @todo Resolve the issue when the test is not passed after Auth implementation
    public function test_user_can_delete_task(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $task = Task::factory()->create();

        $response = $this->deleteJson('/api/v2/tasks/' . $task->id);

        $response->assertNoContent();
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
}
