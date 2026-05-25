<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\SmsMessage;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function __construct(private SmsService $smsService) {}

    public function send(Request $request): JsonResponse
    {
        $client = $request->attributes->get('client');
        $apiKey = $request->attributes->get('api_key');

        $data = $request->validate([
            'to'          => 'required|string|max:20',
            'message'     => 'required|string|max:1600',
            'device_id'   => 'nullable|integer|exists:devices,id',
            'priority'    => 'nullable|integer|min:1|max:10',
            'max_attempts' => 'nullable|integer|min:1|max:5',
        ]);

        $device = null;
        if (!empty($data['device_id'])) {
            $device = Device::where('id', $data['device_id'])
                ->where('client_id', $client->id)
                ->first();
            if (!$device) {
                return response()->json(['success' => false, 'error' => 'Device not found or not yours'], 404);
            }
        }

        $result = $this->smsService->createMessage($data, $client, $apiKey, $device);

        if (!$result['success']) {
            return response()->json($result, 422);
        }

        $msg = $result['message'];

        return response()->json([
            'success'    => true,
            'message_id' => $msg->id,
            'status'     => $msg->status,
        ], 201);
    }

    public function show(Request $request, SmsMessage $smsMessage): JsonResponse
    {
        $client = $request->attributes->get('client');

        if ($smsMessage->client_id !== $client->id) {
            return response()->json(['success' => false, 'error' => 'Not found'], 404);
        }

        return response()->json(['success' => true, 'message' => $smsMessage]);
    }

    public function index(Request $request): JsonResponse
    {
        $client = $request->attributes->get('client');

        $messages = SmsMessage::where('client_id', $client->id)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json(['success' => true, 'data' => $messages]);
    }
}
