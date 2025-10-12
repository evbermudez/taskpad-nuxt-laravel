<?php

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Auth::login($this->user);
});

/**
 * INDEX
 * Users can fetch their own tasks by date
 */
test('a user can fetch tasks for a given date', function () {
    $task = Task::factory()->create([
        'user_id' => $this->user->id,
        'task_date' => now()->toDateString(),
    ]);

    $response = $this->actingAs($this->user)->getJson('/api/tasks?date=' . now()->toDateString());

    $response->assertOk()
             ->assertJsonFragment([
                 'id' => $task->id,
                 'statement' => $task->statement,
             ]);
});

/**
 * STORE
 * Users can create a task
 */
test('a user can create a task', function () {
    $payload = [
        'statement' => 'Write Pest tests',
        'task_date' => now()->toDateString(),
    ];

    $response = $this->actingAs($this->user)->postJson('/api/tasks', $payload);

    $response->assertCreated()
             ->assertJsonFragment(['statement' => 'Write Pest tests']);

    $this->assertDatabaseHas('tasks', [
        'statement' => 'Write Pest tests',
        'user_id' => $this->user->id,
    ]);
});

/**
 * VALIDATION
 * Task creation requires statement and valid date
 */
test('task creation requires valid data', function () {
    $response = $this->actingAs($this->user)->postJson('/api/tasks', []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['statement', 'task_date']);
});

/**
 * UPDATE
 * Users can update their own task
 */
test('a user can update their own task', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)->putJson("/api/tasks/{$task->id}", [
        'statement' => 'Updated task text',
    ]);

    $response->assertOk()->assertJsonFragment(['statement' => 'Updated task text']);
    $this->assertDatabaseHas('tasks', ['id' => $task->id, 'statement' => 'Updated task text']);
});

/**
 * AUTHORIZATION
 * Users cannot update others' tasks
 */
test('a user cannot update another user\'s task', function () {
    $other = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $other->id]);

    $response = $this->actingAs($this->user)->putJson("/api/tasks/{$task->id}", [
        'statement' => 'Hacked!',
    ]);

    $response->assertForbidden();
});

/**
 * TOGGLE
 * Users can toggle their task completion
 */
test('a user can toggle a task', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id, 'is_done' => false]);

    $this->actingAs($this->user)->postJson("/api/tasks/{$task->id}/toggle")->assertOk();

    expect($task->fresh()->is_done)->toBeTrue();
});

/**
 * DESTROY
 * Users can delete their task
 */
test('a user can delete their task', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id]);

    $this->actingAs($this->user)->deleteJson("/api/tasks/{$task->id}")
         ->assertNoContent();

    $this->assertSoftDeleted('tasks', ['id' => $task->id]);
});

/**
 * REORDER
 * Users can reorder tasks for a date
 */
test('a user can reorder their tasks', function () {
    $tasks = Task::factory()->count(3)->create(['user_id' => $this->user->id, 'task_date' => now()->toDateString()]);

    $orders = $tasks->shuffle()->values()->map(fn ($t, $i) => [
        'id' => $t->id,
        'position' => $i + 1,
    ])->toArray();

    $response = $this->actingAs($this->user)->postJson('/api/tasks/reorder', [
        'date' => now()->toDateString(),
        'orders' => $orders,
    ])->assertNoContent();
});

/**
 * SEARCH
 * Users can search their own tasks
 */
test('a user can search their tasks', function () {
    Task::factory()->create([
        'user_id' => $this->user->id,
        'statement' => 'Learn Laravel Testing',
    ]);

    $response = $this->actingAs($this->user)->getJson('/api/search?q=Testing');

    $response->assertOk()
             ->assertJsonFragment(['statement' => 'Learn Laravel Testing']);
});
