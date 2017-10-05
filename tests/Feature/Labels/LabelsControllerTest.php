<?php

namespace Tests\Feature\Labels;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Label;
use App\Models\User;
use App\Models\Color;
use Log;

class LabelsControllerTest extends TestCase
{
    use DatabaseTransactions;
    
    public function testGetLabels()
    {
        $faker = \Faker\Factory::create();
        $user = factory(User::class)->create();
        $colors = factory(Color::class, 12)->create();
        $colorsIds = $colors->pluck('id');

        $labels = collect([]);
        for ($i = 0; $i<10; $i++) {
            $label = factory(Label::class)->create([
                'user_id' => $user->id,
                'color_id' => $faker->numberBetween(
                    $colorsIds->min(),
                    $colorsIds->max()
                )
            ]);
            $labels->push($label);
        }
        $response = $this->getJson('/api/labels', $this->headers($user));
        $response->assertStatus(200)->assertExactJson($labels->toArray());
    }

    public function testStoreLabel()
    {
        $user = factory(User::class)->create();
        $color = factory(Color::class)->create();
        $label = [
            'name' => 'Urgent',
            'color_id' => $color->id,
        ];
        $response = $this->postJson('/api/labels', $label, $this->headers($user));
        $response->assertStatus(200)->assertJson($label);
    }
    public function testPutLabel()
    {
       
        $user = factory(User::class)->create();
        $color = factory(Color::class)->create();
        $label = factory(Label::class)->create([
            'user_id' => $user->id,
            'color_id' => $color->id,
            'name' => 'Urgent',
        ]);
        
        $newColor = factory(Color::class)->create();
        $data = [
            'name' => 'New Urgent',
            'color_id' => $newColor->id,
        ];

        $response = $this->putJson(
            '/api/labels/'.$label->id,
            $data,
            $this->headers($user)
        );
        $response->assertStatus(200)->assertJson($data);
    }
    
    public function testAttachLabelToProject()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        // $user = factory(User::class)->create();
        // $task = factory(Label::class)->create([
        // 'user_id' => $user->id
        // ]);
        // $color = factory(Color::class)->create();
        // $project = factory(Project::class)->create([
        // 'user_id' => $user->id,
        // 'color_id' => $color->id
        // ]);
        // $data = [
        // 'project_id' => $project->id,
        // ];
        // $response = $this->putJson(
        // '/api/labels/'.$task->id,
        // $data,
        // $this->headers($user)
        // );
        // $response->assertStatus(200)->assertJson($data);
    }

    public function testAttachLabelToTask()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
    
    public function testDeleteLabel()
    {
        $user = factory(User::class)->create();
        $color = factory(Color::class)->create();
        $label = factory(Label::class)->create([
            'user_id' => $user->id,
            'color_id' => $color->id,
            'name' => 'Urgent',
        ]);
        $response = $this->deleteJson(
            '/api/labels/'.$label->id,
            [],
            $this->headers($user)
        );
        $response->assertStatus(200);
    }
}
