<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        return $user;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function redirectToProvider($provider)
    {
        $url =  Socialite::with($provider)->stateless()->redirect()->getTargetUrl();
        return response()->json([
            'url' => $url
        ]);
    }

    public function handleProviderCallback($provider)
    {
        $user = Socialite::with($provider)->stateless()->user();
        $authUser = $this->findOrCreateUser($user, $provider);
        $token = JWTAuth::fromUser($authUser);

        return response([
            'token' => $token,
            'user' => $authUser
        ]);
    }

    private function findOrCreateUser($user, $provider)
    {
        $authUser = User::where('provider_id', $user->id)->orWhere('email',$user->email)->first();
        if ($authUser) {
            return $authUser;
        }
        $user = User::create([
            'email'    => $user->email,
            'provider' => $provider,
            'provider_id' => $user->id
        ]);
        return $user;
    }
}
