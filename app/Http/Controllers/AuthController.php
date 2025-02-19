<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\ResponseService;



class AuthController extends Controller
{
    /*
      The response service instance.
     
     */
    protected $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }


    public function register(RegisterRequest $registerRequest)
    {
        // Create a new user instance and save it to the database
        $user = User::create([
            'name' => $registerRequest->name,
            'email' => $registerRequest->email,
            'password' => bcrypt($registerRequest->password),
        ]);

        // Log the user in 
        $token = Auth::login($user);


        return $this->responseService->success('Registered successfully', [
            'user' => $user,
            'token' => $token,
        ], 201);
    }


    public function login(LoginRequest $loginRequest)
    {
        // Extract the email and password from the request
        $credentials = $loginRequest->only('email', 'password');

        // Attempt to log the user in
        if (!$token = Auth::attempt($credentials)) {
            // If the credentials are invalid, return an error response
            return $this->responseService->error('Invalid credentials', [], 401);
        }

        // Return a successful response with the token
        return $this->respondWithToken($token);
    }


    public function me()
    {
        // Return a successful response with the authenticated user
        return $this->responseService->success('User fetched successfully', Auth::user());
    }


    public function logout()
    {
        // Log the user out
        Auth::logout();

        return $this->responseService->success('Successfully logged out');
    }


    public function refresh()
    {
        // Refresh the token and return a successful response
        return $this->respondWithToken(Auth::refresh());
    }


    protected function respondWithToken($token)
    {
        // Return a successful response with the token details
        return $this->responseService->success('Login successfully', [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }
}
