<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserRequestSocialURLTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetGoogleUrl()
    {
        $response = $this->json('GET', '/api/auth/google');
        $response->assertJsonFragment(['url']);
        $response->assertStatus(200);
    }

    public function testGetFacebookUrl()
    {
        $response = $this->json('GET', '/api/auth/facebook');
        $response->assertJsonFragment(['url']);
        $response->assertStatus(200);
    }
}
