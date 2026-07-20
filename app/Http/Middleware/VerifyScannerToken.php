<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyScannerToken
{
    /**
     * Protects the fingerprint-scanner API endpoint. The scanner's local
     * app/SDK sends the shared secret as a Bearer token (preferred) or a
     * ?token= query param (fallback, for devices that can't set headers).
     *
     * Configure the secret via SCANNER_API_TOKEN in .env.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('attendance.scanner_token');

        if (empty($expected)) {
            return response()->json([
                'message' => 'Scanner API is not configured. Set SCANNER_API_TOKEN in .env.',
            ], 500);
        }

        $provided = $request->bearerToken() ?? $request->query('token');

        if (! $provided || ! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Invalid or missing scanner token.'], 401);
        }

        return $next($request);
    }
}
