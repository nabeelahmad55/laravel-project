<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    public function test_can_create_task()
    {
        $project = Project::factory()->create(['team_id' => $this->user->teams()->first()->id]);
        
        $response = $this->postJson('/api/v1/tasks', [
            'title' => 'New Task',
            'description' => 'Task description',
            'project_id' => $project->id,
            'priority' => 'high'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => ['id', 'title', 'priority', 'project']
                 ]);
    }

    public function test_can_list_tasks()
    {
        Task::factory()->count(5)->create([
            'project_id' => Project::factory()->create(['team_id' => $this->user->teams()->first()->id])->id
        ]);

        $response = $this->getJson('/api/v1/tasks');

        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data');
    }

    public function test_can_update_task()
    {
        $task = Task::factory()->create([
            'project_id' => Project::factory()->create(['team_id' => $this->user->teams()->first()->id])->id
        ]);

        $response = $this->putJson("/api/v1/tasks/{$task->id}", [
            'title' => 'Updated Task Title',
            'status' => 'in_progress'
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.title', 'Updated Task Title')
                 ->assertJsonPath('data.status', 'in_progress');
    }
}