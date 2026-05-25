<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Device;
use App\Models\DeviceCommand;
use App\Models\SmsLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function index(Request $request): View
    {
        $devices = Device::with('client')
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('phone_number', 'like', "%{$request->search}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->client_id, fn ($q) => $q->where('client_id', $request->client_id))
            ->orderBy('name')
            ->paginate(20);

        $clients = Client::orderBy('name')->get(['id', 'name']);

        return view('admin.devices.index', compact('devices', 'clients'));
    }

    public function create(): View
    {
        $clients = Client::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        return view('admin.devices.create', compact('clients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'client_id'                  => 'required|exists:clients,id',
            'name'                       => 'required|string|max:100',
            'phone_number'               => 'nullable|string|max:20',
            'gateway_enabled'            => 'boolean',
            'heartbeat_interval_seconds' => 'required|integer|min:10|max:300',
            'pull_interval_seconds'      => 'required|integer|min:3|max:60',
        ]);

        $data['pairing_code'] = Device::generatePairingCode();
        $data['status'] = 'offline';

        $device = Device::create($data);

        return redirect()->route('admin.devices.show', $device)
            ->with('success', "Device created. Pairing code: {$device->pairing_code}");
    }

    public function show(Device $device): View
    {
        $device->load('client');
        $recentMessages = $device->smsMessages()->orderBy('created_at', 'desc')->limit(10)->get();
        $recentLogs     = $device->smsLogs()->orderBy('created_at', 'desc')->limit(20)->get();
        $pendingCommands = $device->commands()->where('status', 'pending')->get();

        return view('admin.devices.show', compact('device', 'recentMessages', 'recentLogs', 'pendingCommands'));
    }

    public function edit(Device $device): View
    {
        $clients = Client::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        return view('admin.devices.edit', compact('device', 'clients'));
    }

    public function update(Request $request, Device $device): RedirectResponse
    {
        $data = $request->validate([
            'name'                       => 'required|string|max:100',
            'phone_number'               => 'nullable|string|max:20',
            'heartbeat_interval_seconds' => 'required|integer|min:10|max:300',
            'pull_interval_seconds'      => 'required|integer|min:3|max:60',
        ]);

        $device->update($data);

        return redirect()->route('admin.devices.show', $device)->with('success', 'Device updated.');
    }

    public function destroy(Device $device): RedirectResponse
    {
        $device->delete();
        return redirect()->route('admin.devices.index')->with('success', 'Device deleted.');
    }

    public function toggleGateway(Device $device): RedirectResponse
    {
        $enabled = !$device->gateway_enabled;
        $device->update(['gateway_enabled' => $enabled]);

        DeviceCommand::create([
            'device_id' => $device->id,
            'command'   => $enabled ? 'enable_gateway' : 'disable_gateway',
            'status'    => 'pending',
            'created_at' => now(),
        ]);

        $action = $enabled ? 'enabled' : 'disabled';
        return back()->with('success', "Gateway {$action}.");
    }

    public function toggleStatus(Device $device): RedirectResponse
    {
        $newStatus = $device->status === 'disabled' ? 'offline' : 'disabled';
        $device->update(['status' => $newStatus]);

        $action = $newStatus === 'disabled' ? 'disabled' : 'enabled';
        return back()->with('success', "Device {$action}.");
    }

    public function regenerateToken(Device $device): RedirectResponse
    {
        $token = Device::generateToken();
        $device->update(['device_token' => $token]);

        SmsLog::create([
            'client_id' => $device->client_id,
            'device_id' => $device->id,
            'type'      => 'device_registered',
            'level'     => 'warning',
            'message'   => "Device token regenerated for {$device->name}",
        ]);

        return back()->with('success', "Token regenerated. New token: {$token}");
    }

    public function regeneratePairingCode(Device $device): RedirectResponse
    {
        $device->update(['pairing_code' => Device::generatePairingCode(), 'device_token' => null]);
        return back()->with('success', "Pairing code regenerated: {$device->pairing_code}");
    }

    public function sendCommand(Request $request, Device $device): RedirectResponse
    {
        $data = $request->validate([
            'command' => 'required|in:' . implode(',', DeviceCommand::AVAILABLE_COMMANDS),
            'payload' => 'nullable|array',
        ]);

        DeviceCommand::create([
            'device_id'  => $device->id,
            'command'    => $data['command'],
            'payload'    => $data['payload'] ?? null,
            'status'     => 'pending',
            'created_at' => now(),
        ]);

        SmsLog::create([
            'client_id' => $device->client_id,
            'device_id' => $device->id,
            'type'      => 'command_created',
            'level'     => 'info',
            'message'   => "Command '{$data['command']}' sent to {$device->name}",
        ]);

        return back()->with('success', "Command '{$data['command']}' queued.");
    }
}
