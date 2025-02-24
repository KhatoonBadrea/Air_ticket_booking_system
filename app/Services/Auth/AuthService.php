<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function login($credentials)
    {
        if (!$token = auth()->attempt($credentials)) {
            return [
                'error' => true,
                'message' => 'Unauthorized',
                'status' => 401,
            ];
        }

        return [
            'error' => false,
            'data' => $this->respondWithToken($token),
            'status' => 200,
        ];
    }

    public function register($data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        $token = JWTAuth::fromUser($user);

        return [
            'message' => 'User created successfully',
            'token' => $token,
            'status' => 201,
        ];
    }

    public function logout()
    {
        auth()->logout();
        return ['message' => 'Successfully logged out', 'status' => 200];
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ];
    }

    public function createNewToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ];
    }
}
