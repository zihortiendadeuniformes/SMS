<?php

namespace App\Http\Controllers\Api\Device;

use App\Http\Controllers\Controller;
use App\Models\SmsLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $device = $request->attributes->get('device');

        $entries = $request->validate([
            'logs'              => 'required|array|max:50',
            'logs.*.type'       => 'required|string|max:60',
            'logs.*.level'      => 'required|in:info,warning,error',
            'logs.*.message'    => 'required|string|max:1000',
            'logs.*.context'    => 'nullable|array',
        ]);

        foreach ($entries['logs'] as $entry) {
            SmsLog::create([
                'client_id'  => $device->client_id,
                'device_id'  => $device->id,
                'type'       => $entry['type'],
                'level'      => $entry['level'],
                'message'    => $entry['message'],
                'context'    => $entry['context'] ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return response()->json(['success' => true, 'saved' => count($entries['logs'])]);
    }
}
