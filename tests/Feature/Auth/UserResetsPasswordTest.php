<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;


class UserResetsPasswordTest extends TestCase
{
    use DatabaseTransactions;

    public function testPasswordReset()
    {
        $user = factory(User::class)->create();

        $token = hash_hmac('sha256', Str::random(255), $user);

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => bcrypt($token)
        ]);
        $response = $this->json('POST', 'api/password/reset', [
            'email'                 => $user->email,
            'password'              => 'new_user_password',
            'password_confirmation' => 'new_user_password',
            'token'                 => $token
        ]);
        $response->assertStatus(200);
    }

    public function testValidationParameters()
    {
        $user = factory(User::class)->create();
        $token = hash_hmac('sha256', Str::random(255), $user);

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => bcrypt($token)
        ]);

        //Check password_confirmation
        $response = $this->json('POST', 'api/password/reset', [
            'email' => $user->email,
            'password' => 'qweqwe',
            'token' => $token
        ]);
        $response->assertStatus(422);

        //Check password
        $response = $this->json('POST', 'api/password/reset', [
            'email' => $user->email,
            'password_confirmation' => 'qweqwe',
            'token' => $token
        ]);
        $response->assertStatus(422);

        //Check email
        $response = $this->json('POST', 'api/password/reset', [
            'password' => 'qweqwe',
            'token' => $token
        ]);
        $response->assertStatus(422);

        //Check token
        $response = $this->json('POST', 'api/password/reset', [
            'email' => $user->email,
            'password' => 'qweqwe',
        ]);
        $response->assertStatus(422);

    }
}
