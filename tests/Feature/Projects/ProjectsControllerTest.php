<?php

namespace Tests\Feature\Projects;

use App\Models\Project;
use App\Models\User;
use App\Models\Color;
use App\Models\Task;
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
        $color = factory(Color::class)->create();
        $projects = factory(Project::class, 10)->create([
            'user_id' => $user->id,
            'color_id' => $color->id
        ]);
        $response = $this->getJson('/api/projects', $this->headers($user));
        $response->assertStatus(200)
            ->assertJson($projects->toArray())
            ->assertJsonFragment(['color' => $color->toArray()]);
    }

    public function testGetProject()
    {
        $user = factory(User::class)->create();
        $color = factory(Color::class)->create();
        $project = factory(Project::class)->create([
            'user_id' => $user->id,
            'color_id' => $color->id
        ]);
        $tasks = factory(Task::class, 10)->create([
            'project_id' => $project->id,
            'user_id' => $user->id
        ]);
        
        $response = $this->getJson('/api/projects/'.$project->id, $this->headers($user));

        $response->assertStatus(200)
            ->assertJson($project->toArray())
            ->assertJson(['color' => $color->toArray()])
            ->assertJson(['tasks' => $tasks->toArray()]);
    }

    public function testStoreProject()
    {
        $user = factory(User::class)->create();
        $color = factory(Color::class)->create();
        $project = [
            'name' => 'test project',
            'color_id' => $color->id
        ];
        $response = $this->postJson('/api/projects', $project, $this->headers($user));
        $response->assertStatus(200)->assertJson($project);
    }
    public function testPutProject()
    {
        $user = factory(User::class)->create();
        $color = factory(Color::class)->create();
        $project = factory(Project::class)->create([
            'user_id' => $user->id,
            'color_id' => $color->id
        ]);
        $newColor = factory(Color::class)->create();
        $data = [
            'name' => 'New description',
            'color_id' => $newColor->id,
            'archived' => 0
        ];
        $response = $this->putJson('/api/projects/'.$project->id, $data, $this->headers($user));
        $response->assertStatus(200)->assertJson($data);
    }

    public function testDeleteProject()
    {
        $user = factory(User::class)->create();
        $color = factory(Color::class)->create();
        $project = factory(Project::class)->create([
            'user_id' => $user->id,
            'color_id' => $color->id
        ]);
        $response = $this->deleteJson('/api/projects/'.$project->id, [], $this->headers($user));
        $response->assertStatus(200);
    }
}
