<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $response = $this->authService->login($credentials);

        if ($response['error']) {
            return $this->error(null, 'Password or email error', 401);
        }

        // dd(auth()->user('api')->getPermissionNames());
        // dd($response['status']);
        return $this->success($response['data'], 'Login successful', $response['status']);
    }

    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();
        $response = $this->authService->register($validatedData);

        return $this->success($response, 'User registered successfully', $response['status']);
    }

    public function logout()
    {
        $response = $this->authService->logout();
        return $this->success(null, $response['message'], $response['status']);
    }

    public function refresh()
    {
        $response = $this->authService->refresh();
        return $this->success($response, 'Token refreshed successfully');
    }

    public function me(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return $this->error(null, 'User not found', 404);
            }

            return $this->success([
                'user' => new UserResource($user)
            ], 'User retrieved successfully');
        } catch (JWTException $e) {
            return $this->error(null, 'Token is invalid', 401);
        }
    }
}
