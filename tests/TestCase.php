<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected static $migrationsRun = false;


    //TODO: Properly setup testing database (find the side effects)
    public function setUp()
    {
        parent::setUp();
        if (!static::$migrationsRun) {
            Log::info('run');
            Artisan::call('migrate');
            static::$migrationsRun = true;
        }
    }

    protected function headers($user = null)
    {
        $headers = ['Accept' => 'application/json'];

        if (!is_null($user)) {
            $token = JWTAuth::fromUser($user);
            JWTAuth::setToken($token);
            $headers['Authorization'] = 'Bearer '.$token;
        }

        return $headers;
    }
}
