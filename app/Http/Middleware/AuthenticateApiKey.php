<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use App\Models\SmsLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('X-API-KEY');

        if (!$key) {
            return response()->json(['success' => false, 'error' => 'Missing API key'], 401);
        }

        $apiKey = ApiKey::where('api_key', $key)->with('client')->first();

        if (!$apiKey) {
            SmsLog::create([
                'type' => 'auth_failed',
                'level' => 'warning',
                'message' => 'Invalid API key attempt',
                'context' => ['api_key' => substr($key, 0, 10) . '...'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            return response()->json(['success' => false, 'error' => 'Invalid API key'], 401);
        }

        if (!$apiKey->isActive()) {
            return response()->json(['success' => false, 'error' => 'API key is inactive'], 403);
        }

        if (!$apiKey->client->isActive()) {
            return response()->json(['success' => false, 'error' => 'Client account is inactive'], 403);
        }

        if (!$apiKey->isAllowedFromIp($request->ip())) {
            SmsLog::create([
                'client_id' => $apiKey->client_id,
                'type' => 'permission_error',
                'level' => 'warning',
                'message' => 'API request from non-whitelisted IP',
                'context' => ['ip' => $request->ip()],
                'ip_address' => $request->ip(),
            ]);
            return response()->json(['success' => false, 'error' => 'IP not allowed'], 403);
        }

        $secret = $request->header('X-API-SECRET');
        if ($apiKey->api_secret && $secret !== $apiKey->api_secret) {
            return response()->json(['success' => false, 'error' => 'Invalid API secret'], 401);
        }

        $request->attributes->set('api_key', $apiKey);
        $request->attributes->set('client', $apiKey->client);

        return $next($request);
    }
}
