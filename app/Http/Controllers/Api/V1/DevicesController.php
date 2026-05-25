<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DevicesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $client = $request->attributes->get('client');

        $devices = Device::where('client_id', $client->id)
            ->where('status', '!=', 'disabled')
            ->get(['id', 'name', 'phone_number', 'status', 'gateway_enabled', 'battery_level', 'signal_strength', 'last_seen_at']);

        return response()->json(['success' => true, 'devices' => $devices]);
    }
}
