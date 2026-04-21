<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class LogApiRequests
{
    /**
     * Fields to mask in logs (sensitive data).
     */
    protected array $sensitiveFields = [
        'password',
        'password_confirmation',
        'token',
        'secret',
        'card_number',
        'cvv',
        'api_key',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $startTime = microtime(true);

        /** @var SymfonyResponse $response */
        $response = $next($request);

        $durationMs = round((microtime(true) - $startTime) * 1000, 2);

        $this->logRequest($request, $response, $durationMs);

        return $response;
    }

    protected function logRequest(Request $request, SymfonyResponse $response, float $durationMs): void
    {
        try {
            $user = null;
            try {
                $user = $request->user();
            } catch (\Throwable) {}

            $requestBody = $this->sanitize($request->except(['password', 'password_confirmation']));

            // Limit response body size (avoid logging huge responses)
            $responseBody = null;
            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $decoded = $response->getData(true);
                // Only log if not too large
                if (is_array($decoded) && json_encode($decoded) !== false) {
                    $responseBody = strlen(json_encode($decoded)) <= 5000 ? $decoded : '[Response too large to log]';
                }
            }

            $statusCode = $response->getStatusCode();
            $level      = $this->resolveLogLevel($statusCode);

            Log::channel('api')->$level('[API] {method} {url}', [
                'method'          => $request->method(),
                'url'             => $request->fullUrl(),
                'route'           => optional($request->route())->getName() ?? $request->path(),
                'status'          => $statusCode,
                'duration_ms'     => $durationMs,
                'ip'              => $request->ip(),
                'user_agent'      => $request->userAgent(),
                'user_id'         => $user?->id,
                'user_email'      => $user?->email,
                'request_body'    => $requestBody,
                'response_status' => $statusCode,
                'response_body'   => $responseBody,
            ]);

            // Also mirror to api_errors channel for 4xx / 5xx responses
            if ($statusCode >= 400) {
                Log::channel('api_errors')->$level('[API ERROR] {method} {url}', [
                    'method'          => $request->method(),
                    'url'             => $request->fullUrl(),
                    'status'          => $statusCode,
                    'duration_ms'     => $durationMs,
                    'ip'              => $request->ip(),
                    'user_id'         => $user?->id,
                    'user_email'      => $user?->email,
                    'request_body'    => $requestBody,
                    'response_body'   => $responseBody,
                ]);
            }
        } catch (\Throwable $e) {
            // Never let logging crash the application
            Log::error('[ApiLogger] Logging failed: ' . $e->getMessage());
        }
    }

    /**
     * Mask sensitive fields in the payload.
     */
    protected function sanitize(array $data): array
    {
        foreach ($this->sensitiveFields as $field) {
            if (array_key_exists($field, $data)) {
                $data[$field] = '***MASKED***';
            }
        }
        return $data;
    }

    /**
     * Resolve log level from HTTP status code.
     */
    protected function resolveLogLevel(int $status): string
    {
        if ($status >= 500) {
            return 'error';
        }
        if ($status >= 400) {
            return 'warning';
        }
        return 'info';
    }
}
