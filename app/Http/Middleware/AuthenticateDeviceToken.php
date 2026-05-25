<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateDeviceToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Device-Token') ?? $request->bearerToken();

        if (!$token) {
            return response()->json(['success' => false, 'error' => 'Missing device token'], 401);
        }

        $device = Device::where('device_token', $token)->with('client')->first();

        if (!$device) {
            return response()->json(['success' => false, 'error' => 'Invalid device token'], 401);
        }

        if ($device->isDisabled()) {
            return response()->json(['success' => false, 'error' => 'Device is disabled'], 403);
        }

        $request->merge(['authenticated_device' => $device]);
        $request->attributes->set('device', $device);

        return $next($request);
    }
}
