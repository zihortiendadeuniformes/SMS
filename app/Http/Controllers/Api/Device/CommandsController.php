<?php

namespace App\Http\Controllers\Api\Device;

use App\Http\Controllers\Controller;
use App\Models\DeviceCommand;
use App\Models\SmsLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommandsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $device = $request->attributes->get('device');

        $commands = DeviceCommand::where('device_id', $device->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get(['id', 'command', 'payload', 'created_at']);

        return response()->json(['success' => true, 'commands' => $commands]);
    }

    public function ack(Request $request, DeviceCommand $command): JsonResponse
    {
        $device = $request->attributes->get('device');

        if ($command->device_id !== $device->id) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $command->update(['status' => 'received', 'received_at' => now()]);

        SmsLog::create([
            'device_id' => $device->id,
            'type'      => 'command_received',
            'level'     => 'info',
            'message'   => "Command #{$command->id} ({$command->command}) received by device",
        ]);

        return response()->json(['success' => true]);
    }

    public function result(Request $request, DeviceCommand $command): JsonResponse
    {
        $device = $request->attributes->get('device');

        if ($command->device_id !== $device->id) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'success'  => 'required|boolean',
            'response' => 'nullable|array',
            'error'    => 'nullable|string|max:500',
        ]);

        if ($data['success']) {
            $command->update([
                'status'      => 'executed',
                'response'    => $data['response'] ?? null,
                'executed_at' => now(),
            ]);

            SmsLog::create([
                'device_id' => $device->id,
                'type'      => 'command_executed',
                'level'     => 'info',
                'message'   => "Command #{$command->id} ({$command->command}) executed successfully",
            ]);
        } else {
            $command->update([
                'status'        => 'failed',
                'error_message' => $data['error'] ?? 'Unknown error',
                'failed_at'     => now(),
            ]);

            SmsLog::create([
                'device_id' => $device->id,
                'type'      => 'command_failed',
                'level'     => 'error',
                'message'   => "Command #{$command->id} ({$command->command}) failed: " . ($data['error'] ?? 'Unknown'),
            ]);
        }

        return response()->json(['success' => true]);
    }
}
