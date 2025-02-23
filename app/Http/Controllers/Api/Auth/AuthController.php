<?php
namespace App\Http\Controllers\Api\Auth;

use App\Services\Auth\AuthService;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\Auth\RegisterRequest;

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

            return $this->success(['user' => $user], 'User retrieved successfully');
        } catch (JWTException $e) {
            return $this->error(null, 'Token is invalid', 401);
        }
    }
}