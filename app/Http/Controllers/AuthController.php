<?php

namespace App\Http\Controllers;

use App\Helper\ApiResponser ;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $user =  $this->authService->register($data);
        return ApiResponser::successResponse('User Created',200,new UserResource($user));
    }

    public function login(AuthRequest $request)
    {
        $data = $request->validated();
        $user = $this->authService->login($data);
        return ApiResponser::successResponse('Success Login',200,new UserResource($user));
    }

    public function logout()
    {
        $this->authService->logout();
        return ApiResponser::successResponse('Success Logout');
    }
}
