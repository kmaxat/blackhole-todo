<?php

namespace Tests\Feature\Projects;

use App\Models\Project;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProjectsControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetAllProjects()
    {
        $user = factory(User::class)->create();
        $projects = factory(Project::class, 10)->create([
            'user_id' => $user->id
        ]);
        $response = $this->getJson('/api/tasks',$this->headers($user));
        $response->assertStatus(200)->assertJson($projects->toArray());
    }

    public function testGetProject()
    {
        $user = factory(User::class)->create();
        $task = factory(Project::class)->create([
            'user_id' => $user->id
        ]);
        $response = $this->getJson('/api/tasks/'.$task->id,$this->headers($user));

        $response->assertStatus(200)->assertJson($task->toArray());
    }

    public function testStoreProject()
    {
        $user = factory(User::class)->create();
        $task = [
            'description' => 'Test description',
            'priority' => 1,
        ];
        $response = $this->postJson('/api/tasks', $task, $this->headers($user));
        $response->assertStatus(200)->assertJson($task);
    }
    public function testPutProject()
    {
        $user = factory(User::class)->create();
        $task = factory(Project::class)->create([
            'user_id' => $user->id
        ]);
        $data = [
            'description' => 'New description',
            'priority' => 2,
        ];
        $response = $this->putJson('/api/tasks/'.$task->id, $data, $this->headers($user));
        $response->assertStatus(200)->assertJson($data);
    }

    public function testDeleteProject()
    {
        $user = factory(User::class)->create();
        $task = factory(Project::class)->create([
            'user_id' => $user->id
        ]);
        $response = $this->deleteJson('/api/tasks/'.$task->id,[], $this->headers($user));
        $response->assertStatus(200);
    }
}
