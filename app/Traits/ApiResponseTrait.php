<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{
    /**
     * Success response with data.
     */
    protected function success(
        $data = null,
        string $message = 'Success',
        int $status = Response::HTTP_OK
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ];

        return response()->json($response, $status);
    }

    /**
     * Error response.
     */
    protected function error(
        string $message = 'Error',
        int $status = Response::HTTP_BAD_REQUEST,
        array $errors = []
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Resource response (single item).
     */
    protected function resource(
        JsonResource $resource,
        string $message = 'Success',
        int $status = Response::HTTP_OK
    ): JsonResponse {
        return $this->success($resource, $message, $status);
    }

    /**
     * Collection response (list).
     */
    protected function collection(
        ResourceCollection $collection,
        string $message = 'Success',
        int $status = Response::HTTP_OK
    ): JsonResponse {
        return $this->success($collection, $message, $status);
    }

    /**
     * Created response (201).
     */
    protected function created($data = null, string $message = 'Created'): JsonResponse
    {
        return $this->success($data, $message, Response::HTTP_CREATED);
    }

    /**
     * No content (204).
     */
    protected function noContent(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Validation error (422).
     */
    protected function validationError(array $errors, string $message = 'Validation Error'): JsonResponse
    {
        return $this->error($message, Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
    }

    /**
     * Unauthorized (401).
     */
    protected function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->error($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Forbidden (403).
     */
    protected function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->error($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Not found (404).
     */
    protected function notFound(string $message = 'Not Found'): JsonResponse
    {
        return $this->error($message, Response::HTTP_NOT_FOUND);
    }
}