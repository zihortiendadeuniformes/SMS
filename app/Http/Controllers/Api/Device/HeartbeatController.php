<?php

namespace App\Http\Controllers\Api\Device;

use App\Http\Controllers\Controller;
use App\Services\DeviceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HeartbeatController extends Controller
{
    public function __construct(private DeviceService $deviceService) {}

    public function __invoke(Request $request): JsonResponse
    {
        $device = $request->attributes->get('device');

        $data = $request->validate([
            'battery_level'   => 'nullable|integer|min:0|max:100',
            'signal_strength' => 'nullable|integer|min:0|max:100',
            'sim_operator'    => 'nullable|string|max:100',
            'gateway_enabled' => 'nullable|boolean',
            'app_version'     => 'nullable|string|max:20',
            'android_version' => 'nullable|string|max:20',
        ]);

        $result = $this->deviceService->processHeartbeat($device, $data);

        return response()->json($result);
    }
}
