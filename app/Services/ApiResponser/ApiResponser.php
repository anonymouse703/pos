<?php

namespace App\Services\ApiResponser;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiResponser
{
//    public function validationError(mixed $errors, string $message = null): \Illuminate\Http\JsonResponse
//    {
//        return response()->json([
//            'errors' => $errors,
//            'message' => $message ?? __('Invalid input.'),
//            'success' => false
//        ], 422);
//    }

    public function notFound(string $message = null): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => $message ?? __('Not found.'),
            'success' => false
        ], 404);
    }

    public function badRequest(string $message = null): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => $message ?? __('Bad request.'),
            'success' => false
        ], 400);
    }

    public function forbidden(string $message = null): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => $message ?? __('This action is forbidden.'),
            'success' => false
        ], 403);
    }

    public function throttled(string|null $message = null, int|null $availableInSeconds = null): \Illuminate\Http\JsonResponse
    {
        return response()->json(array_merge([
            'message' => $message ?? __('Too many attempts.')
        ], isset($availableInSeconds) ? ['available_in_seconds' => $availableInSeconds] : []), 429);
    }

    public function failed(string $message, int $code = 500): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => $message,
            'success' => false
        ], $code);
    }

    public function success(string $message = null, array $data = [], int $code = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => $message ?? __('Success.'),
            'data' => $data,
            'success' => true
        ], $code);
    }

    public function successWithResource(string $resource, mixed $data, string $message = null, array $additional = []): JsonResource
    {
        return (new $resource($data))->additional(array_merge($additional, [
            'message' => $message ?? __('Success.'),
            'success' => true
        ]));
    }

    public function successWithResourceCollection(string $jsonResource, mixed $data, string $message = null, array $additional = []): JsonResource
    {
        return $jsonResource::collection($data)->additional(array_merge($additional, [
            'message' => $message ?? __('Success.'),
            'success' => true
        ]));
    }
}
