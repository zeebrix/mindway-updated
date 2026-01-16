<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success(
        string $message,
        mixed $data = null,
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'code' => $code,
            'status' => 'Success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error(
        string $message,
        int $code = 422
    ): JsonResponse {
        return response()->json([
            'code' => $code,
            'status' => 'Error',
            'message' => $message,
        ], $code);
    }
}
