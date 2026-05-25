<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Device;
use App\Models\SmsLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogController extends Controller
{
    public function index(Request $request): View
    {
        try {
            $logs = SmsLog::with(['client', 'device'])
                ->when($request->level, fn ($q) => $q->where('level', $request->level))
                ->when($request->type, fn ($q) => $q->where('type', $request->type))
                ->when($request->device_id, fn ($q) => $q->where('device_id', $request->device_id))
                ->when($request->client_id, fn ($q) => $q->where('client_id', $request->client_id))
                ->when($request->date_from, fn ($q) => $q->where('created_at', '>=', $request->date_from))
                ->when($request->date_to, fn ($q) => $q->where('created_at', '<=', $request->date_to . ' 23:59:59'))
                ->orderBy('created_at', 'desc')
                ->paginate(50);
            $clients = Client::orderBy('name')->get(['id', 'name']);
            $devices = Device::orderBy('name')->get(['id', 'name']);
        } catch (\Throwable $e) {
            $logs    = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 50);
            $clients = collect();
            $devices = collect();
        }

        $logTypes = [
            'device_registered', 'device_heartbeat', 'device_connected', 'device_disconnected',
            'sms_created', 'sms_reserved', 'sms_sent', 'sms_failed',
            'command_created', 'command_received', 'command_executed', 'command_failed',
            'api_request', 'auth_failed', 'permission_error', 'gateway_disabled', 'device_disabled',
        ];

        return view('admin.logs.index', compact('logs', 'clients', 'devices', 'logTypes'));
    }
}
