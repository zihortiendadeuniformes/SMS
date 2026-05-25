<?php

namespace App\Http\Controllers\Api\Device;

use App\Http\Controllers\Controller;
use App\Models\SmsMessage;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function __construct(private SmsService $smsService) {}

    public function pending(Request $request): JsonResponse
    {
        $device = $request->attributes->get('device');

        if (!$device->isGatewayActive()) {
            return response()->json(['success' => true, 'messages' => []]);
        }

        $messages = SmsMessage::where('status', 'pending')
            ->where(function ($q) use ($device) {
                $q->whereNull('device_id')->orWhere('device_id', $device->id);
            })
            ->where('client_id', $device->client_id)
            ->orderBy('priority', 'asc')
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get(['id', 'to_number', 'message_body', 'priority', 'attempts', 'max_attempts']);

        return response()->json(['success' => true, 'messages' => $messages]);
    }

    public function reserve(Request $request, SmsMessage $message): JsonResponse
    {
        $device = $request->attributes->get('device');

        if ((string)$message->client_id !== (string)$device->client_id) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $reserved = $this->smsService->reserveMessage($message, $device);

        if (!$reserved) {
            return response()->json(['success' => false, 'error' => 'Message no longer available']);
        }

        return response()->json(['success' => true, 'message' => $message->fresh()]);
    }

    public function markSent(Request $request, SmsMessage $message): JsonResponse
    {
        $device = $request->attributes->get('device');

        if ($message->device_id !== $device->id) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'provider_response' => 'nullable|array',
        ]);

        $this->smsService->markSent($message, $data['provider_response'] ?? null);

        return response()->json(['success' => true]);
    }

    public function markFailed(Request $request, SmsMessage $message): JsonResponse
    {
        $device = $request->attributes->get('device');

        if ($message->device_id !== $device->id) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'error'             => 'required|string|max:500',
            'provider_response' => 'nullable|array',
        ]);

        $this->smsService->markFailed($message, $data['error'], $data['provider_response'] ?? null);

        return response()->json(['success' => true]);
    }
}
