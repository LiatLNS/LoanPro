<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('OPTIONS')) {
            return response('', 204);
        }

        $secretKey = $request->header('x-api-key');
        
        $storedKey = config('app.api_secret_key') ?? env('API_SECRET_KEY', 'CodeChallengeAccessAPI');

        if (!$secretKey || $secretKey !== $storedKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Invalid or missing API key.'
            ], 401);
        }

        return $next($request);
    }
}