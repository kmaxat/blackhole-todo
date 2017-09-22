<?php

namespace Tests\Feature\Tasks;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Facades\JWTAuth;

class TasksControllerTest extends TestCase
{

    use DatabaseTransactions;

    public function testGetAllTasks()
    {
        $user = factory(User::class)->create();
        $tasks = factory(Task::class, 10)->create([
            'user_id' => $user->id
        ]);
        $response = $this->getJson('/api/tasks',$this->headers($user));
        $response->assertStatus(200)->assertJson($tasks->toArray());
    }

    public function testGetTask()
    {
        $user = factory(User::class)->create();
        $task = factory(Task::class)->create([
            'user_id' => $user->id
        ]);
        $response = $this->getJson('/api/tasks/'.$task->id,$this->headers($user));

        $response->assertStatus(200)->assertJson($task->toArray());
    }

    public function testStoreTask()
    {
        $user = factory(User::class)->create();
        $task = [
            'description' => 'Test description',
            'priority' => 1,
        ];
        $response = $this->postJson('/api/tasks', $task, $this->headers($user));
        $response->assertStatus(200)->assertJson($task);
    }
    public function testPutTask()
    {
        $user = factory(User::class)->create();
        $task = factory(Task::class)->create([
            'user_id' => $user->id
        ]);
        $data = [
            'description' => 'New description',
            'priority' => 2,
        ];
        $response = $this->putJson('/api/tasks/'.$task->id, $data, $this->headers($user));
        $response->assertStatus(200)->assertJson($data);
    }

    public function testDeleteTask()
    {
        $user = factory(User::class)->create();
        $task = factory(Task::class)->create([
            'user_id' => $user->id
        ]);
        $response = $this->deleteJson('/api/tasks/'.$task->id,[], $this->headers($user));
        $response->assertStatus(200);
    }
}
