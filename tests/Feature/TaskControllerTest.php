<?php

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a task', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson('/api/tasks', [
        'title' => 'Test Task',
        'description' => 'Task description',
        'status' => 'pending',
        'due_date' => '2025-03-03', // Example due date
        
    ]);

    $response->assertStatus(201)
             ->assertJson(['message' => 'Task created successfully']);
});

it('can fetch tasks', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    Task::factory()->create(['user_id' => $user->id]);

    $response = $this->getJson('/api/tasks');

    $response->assertStatus(200)
             ->assertJsonStructure(['data']);
});

it('can update a task', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->putJson("/api/tasks/{$task->id}", [
        'title' => 'Updated Task',
        'status' => 'completed', // Example status update
    ]);

    $response->assertStatus(200)
             ->assertJson(['message' => 'Task updated successfully']);
});

it('can delete a task', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(200)
             ->assertJson(['message' => 'Task deleted successfully']);
});

it('cannot create a task with priority if not paid', function () {
    $user = User::factory()->create(); // Create user without is_paid attribute
    $this->actingAs($user);

    $response = $this->postJson('/api/tasks', [
        'title' => 'Test Task',
        'description' => 'Task description',
        'status' => 'pending',
        'due_date' => '2025-03-03',
        'priority' => 'high',
    ]);

    $response->assertStatus(403)
             ->assertJson(['message' => 'Priority feature is available for paid users only']);
});
