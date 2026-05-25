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

        // Auto-confirm: messages reserved by this device >15s ago = mark as sent
        SmsMessage::where('status', 'reserved')
            ->where('device_id', $device->id)
            ->where('reserved_at', '<', now()->subSeconds(15))
            ->update(['status' => 'sent', 'sent_at' => now()]);

        // Auto-fail: messages stuck in reserved for >2 min (markSent AND markFailed both failed)
        SmsMessage::where('status', 'reserved')
            ->where('device_id', $device->id)
            ->where('reserved_at', '<', now()->subMinutes(2))
            ->update(['status' => 'failed', 'failed_at' => now(), 'error_message' => 'Timeout: no confirmation received']);

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

        $reserved = $this->smsService->reserveMessage($message, $device);

        if (!$reserved) {
            return response()->json(['success' => false, 'error' => 'Message no longer available']);
        }

        return response()->json(['success' => true, 'message' => $message->fresh()]);
    }

    public function markSent(Request $request, SmsMessage $message): JsonResponse
    {
        $device = $request->attributes->get('device');

        $data = $request->validate([
            'provider_response' => 'nullable|array',
        ]);

        $this->smsService->markSent($message, $data['provider_response'] ?? null);

        return response()->json(['success' => true]);
    }

    public function markFailed(Request $request, SmsMessage $message): JsonResponse
    {
        $device = $request->attributes->get('device');

        $data = $request->validate([
            'error'             => 'required|string|max:500',
            'provider_response' => 'nullable|array',
        ]);

        $this->smsService->markFailed($message, $data['error'], $data['provider_response'] ?? null);

        return response()->json(['success' => true]);
    }
}
