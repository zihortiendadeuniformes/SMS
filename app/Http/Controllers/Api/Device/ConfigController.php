<?php

namespace App\Http\Controllers\Api\Device;

use App\Http\Controllers\Controller;
use App\Services\DeviceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function __construct(private DeviceService $deviceService) {}

    public function __invoke(Request $request): JsonResponse
    {
        $device = $request->attributes->get('device');

        return response()->json([
            'success' => true,
            'config'  => $this->deviceService->getDeviceConfig($device),
        ]);
    }
}
