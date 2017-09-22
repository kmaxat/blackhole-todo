<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SendsResetEmailTest extends TestCase
{
    use DatabaseTransactions;

    public function testSuccessfulLink()
    {
        $user = factory(User::class)->create();
        $response = $this->json('POST', '/api/password/email', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200);
    }

    public function testValidationParameters()
    {
        $response = $this->json('POST', '/api/password/email', [
        ]);
        $response->assertStatus(422);
    }
}
