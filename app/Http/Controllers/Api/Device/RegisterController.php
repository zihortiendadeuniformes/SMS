<?php

namespace App\Http\Controllers\Api\Device;

use App\Http\Controllers\Controller;
use App\Services\DeviceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __construct(private DeviceService $deviceService) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pairing_code'   => 'required|string',
            'name'           => 'nullable|string|max:100',
            'device_uuid'    => 'nullable|string|max:100',
            'phone_number'   => 'nullable|string|max:20',
            'android_version' => 'nullable|string|max:20',
            'app_version'    => 'nullable|string|max:20',
        ]);

        $result = $this->deviceService->registerDevice(
            $validated,
            strtoupper(trim($validated['pairing_code']))
        );

        if (!$result['success']) {
            return response()->json($result, 422);
        }

        return response()->json($result, 201);
    }
}
