<?php

namespace App\Services\ApiResponser\Facades;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\JsonResponse notFound(string $message = null)
 * @method static \Illuminate\Http\JsonResponse badRequest(string $message = null)
 * @method static \Illuminate\Http\JsonResponse forbidden(string $message = null)
 * @method static \Illuminate\Http\JsonResponse throttled(string|null $message = null, int|null $availableInSeconds = null)
 * @method static \Illuminate\Http\JsonResponse failed(string $message, int $code = 500)
 * @method static \Illuminate\Http\JsonResponse success(string $message = null, array $data = [], int $code = 200)
 * @method static JsonResource successWithResource(string $resource, mixed $data, string $message = null, array $additional = [])
 * @method static JsonResource successWithResourceCollection(string $jsonResource, mixed $data, string $message = null, array $additional = [])
 *
 * @see \App\Services\ApiResponser\ApiResponser
 */
class ApiResponser extends Facade
{
	public static function getFacadeAccessor()
	{
		return 'api-responser';
	}
}
