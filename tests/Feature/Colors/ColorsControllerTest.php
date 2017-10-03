<?php

namespace Tests\Feature\Colors;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Color;

class ColorsControllerTest extends TestCase
{
    use DatabaseTransactions;
    
    public function testGetAllColors()
    {
        $user = factory(User::class)->create();
        $colors = factory(Color::class, 10)->create();
        $response = $this->getJson('/api/colors', $this->headers($user));
        $response->assertStatus(200)
            ->assertJson($colors->toArray());
    }
}
