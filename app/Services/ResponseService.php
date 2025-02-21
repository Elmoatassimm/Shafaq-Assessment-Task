<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{
   
    public function success(string $message ,  $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

   
    public function error(string $message = '', array $errors = [], int $status = 400): JsonResponse
{
    return response()->json([
        'message' => $message,
        'errors' => $errors,
    ], $status);
}


  
}