<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

trait ApiResponse
{
    protected function successResponse(mixed $data, ?string $message = null, int $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response, $status);
    }

    protected function paginatedResponse(AnonymousResourceCollection $collection, ?string $message = null): JsonResponse
    {
        $payload = $collection->response()->getData(true);

        $response = [
            'success' => true,
            'data' => $payload['data'],
            'meta' => $payload['meta'] ?? [],
            'links' => $payload['links'] ?? [],
        ];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response);
    }
}
