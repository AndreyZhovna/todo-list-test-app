<?php

namespace App\Domain\Shared\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Http\Response;

final class ApiResponse
{
    protected ?string $message = null;
    protected mixed $data = null;
    protected ?array $pagination = null;
    protected ?int $code = null;

    protected array $headers = [];
    protected int $options = 0;

    public function ok(mixed $data = null, $message = null): JsonResponse
    {
        return $this->makeResponse($data, $message, Response::HTTP_OK);
    }

    public function created(mixed $data = null, string $message = 'Created.'): JsonResponse
    {
        return $this->makeResponse($data, $message, Response::HTTP_CREATED);
    }

    public function noContent(mixed $data = null, string $message = 'No Content.'): JsonResponse
    {
        return $this->makeResponse($data, $message, Response::HTTP_NO_CONTENT);
    }

    public function movedPermanently(mixed $data = null, string $message = 'Moved permanently.'): JsonResponse
    {
        return $this->makeResponse($data, $message, Response::HTTP_MOVED_PERMANENTLY);
    }

    public function found(mixed $data = null, string $message = 'Found.'): JsonResponse
    {
        return $this->makeResponse($data, $message, Response::HTTP_FOUND);
    }

    public function badRequest(mixed $data = null, string $message = 'Bad Request.'): JsonResponse
    {
        return $this->makeResponse($data, $message, Response::HTTP_BAD_REQUEST);
    }

    public function unauthorized(mixed $data = null, string $message = 'Unauthorized.'): JsonResponse
    {
        return $this->makeResponse($data, $message, Response::HTTP_UNAUTHORIZED);
    }

    public function forbidden(mixed $data = null, string $message = 'Forbidden.'): JsonResponse
    {
        return $this->makeResponse($data, $message, Response::HTTP_FORBIDDEN);
    }

    public function notFound(mixed $data = null, string $message = 'Not Found.'): JsonResponse
    {
        return $this->makeResponse($data, $message, Response::HTTP_NOT_FOUND);
    }

    public function internalServerError(mixed $data = null, string $message = 'Internal Server Error.'): JsonResponse
    {
        return $this->makeResponse($data, $message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function validationFailed(mixed $data = null, string $message = 'Validation failed.'): JsonResponse
    {
        return $this->makeResponse($data, $message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function tooManyRequests(mixed $data = null, string $message = 'Too many requests.'): JsonResponse
    {
        return $this->makeResponse($data, $message, Response::HTTP_TOO_MANY_REQUESTS);
    }

    /**
     * Default response
     * @return JsonResponse
     */
    public function response(): JsonResponse
    {
        return $this->makeResponse($this->data, $this->message, $this->code);
    }

    /**
     * Set data
     * @param mixed $data
     * @return $this
     */
    public function data(mixed $data): ApiResponse
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set message
     * @param string $message
     * @return $this
     */
    public function message(string $message): ApiResponse
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set pagination
     * @param AbstractPaginator $paginator
     * @return $this
     */
    public function pagination(AbstractPaginator $paginator): ApiResponse
    {
        $this->pagination = $this->getPagination($paginator);

        return $this;
    }

    /**
     * Set headers
     * @param array $headers
     * @return $this
     */
    public function headers(array $headers): ApiResponse
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Set HTTP status code
     * @param int $code
     * @return $this
     */
    public function code(int $code): ApiResponse
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param AbstractPaginator $paginator
     * @return array
     */
    protected function getPagination(AbstractPaginator $paginator): array
    {
        return [
            'total' => $paginator->total(),
            'count' => $paginator->count(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem()
        ];
    }

    /**
     * @param mixed $data
     * @param string|null $message
     * @param int $code
     * @return JsonResponse
     */
    protected function makeResponse(mixed $data, ?string $message, int $code): JsonResponse
    {
        $this->data = $this->data ?? $data;
        $this->message = $this->message ?? $message;
        $this->code = $this->code ?? $code;

        $content = [
            'message' => $this->message,
            'data' => $this->data
        ];

        if ($this->pagination) {
            $content['pagination'] = $this->pagination;
        }

        return response()->json($content, $this->code, $this->headers, $this->options);
    }
}
