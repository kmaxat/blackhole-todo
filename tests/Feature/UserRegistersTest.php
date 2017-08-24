<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserRegistersTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccessfulRegistration()
    {
         $response = $this->json('POST', '/api/register', [
             'name' => 'Sally',
             'email' => 'sally@qwe.com',
             'password' => 'qweqwe'
         ]);

        $response->assertStatus(200)->assertJson([
                'name' => 'Sally',
                'email' => 'sally@qwe.com',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'sally@qwe.com'
        ]);
    }

    public function testValidationParameters()
    {

        $response = $this->json('POST', '/api/register', [
            'email' => 'sally@qwe.com',
            'password' => 'qweqwe'
        ]);

        $response->assertStatus(422);

        $response = $this->json('POST', '/api/register', [
            'name' => 'Sally',
            'password' => 'qweqwe'
        ]);

        $response->assertStatus(422);

        $response = $this->json('POST', '/api/register', [
            'name' => 'Sally',
            'email' => 'sally@qwe.com',
        ]);

        $response->assertStatus(422);

    }
}

