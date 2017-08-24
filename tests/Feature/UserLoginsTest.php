<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserLoginsTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccessfulLogin()
    {

        $user = factory(User::class)->create();
        $response = $this->json('POST', '/api/login', [
            'email' => $user->email,
            'password' => 'secret'
        ]);

        $response->assertStatus(200)->assertJson([
            'user' => [
                'email' => $user->email,
            ],
        ]);

    }

    public function testValidationParameters()
    {
        $user = factory(User::class)->create();

        $response = $this->json('POST', '/api/login', [
            'email' => $user->email,
        ]);
        $response->assertStatus(422);

        $response = $this->json('POST', '/api/login', [
            'password' => 'secret'
        ]);

        $response->assertStatus(422);
    }
}
