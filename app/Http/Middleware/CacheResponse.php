<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cache GET responses in Redis for read-heavy authenticated pages.
 * Cache is scoped per user + URL to ensure data isolation.
 * Bypassed automatically for POST/PUT/DELETE and Livewire requests.
 */
class CacheResponse
{
    public function handle(Request $request, Closure $next, int $ttl = 60): Response
    {
        // Only cache GET requests; skip Livewire/AJAX
        if (
            !$request->isMethod('GET') ||
            $request->header('X-Livewire') ||
            $request->expectsJson() ||
            $request->has('_token')
        ) {
            return $next($request);
        }

        // Never cache pages for unauthenticated users — CSRF tokens would leak across sessions
        $userId = optional($request->user())->id;
        if (!$userId) {
            return $next($request);
        }

        $key = 'page_cache:' . $userId . ':' . sha1($request->fullUrl());

        if (Cache::has($key)) {
            $cached = Cache::get($key);
            return response($cached['content'], 200, $cached['headers']);
        }

        $response = $next($request);

        // Only cache successful HTML responses
        if ($response->getStatusCode() === 200 &&
            str_contains($response->headers->get('Content-Type', ''), 'text/html')) {
            Cache::put($key, [
                'content' => $response->getContent(),
                'headers' => ['Content-Type' => 'text/html; charset=UTF-8'],
            ], $ttl);
        }

        return $response;
    }
}
