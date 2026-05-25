<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Device;
use App\Models\SmsMessage;
use App\Services\SmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmsController extends Controller
{
    public function __construct(private SmsService $smsService) {}

    public function index(Request $request): View
    {
        $messages = SmsMessage::with(['client', 'device'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->client_id, fn ($q) => $q->where('client_id', $request->client_id))
            ->when($request->device_id, fn ($q) => $q->where('device_id', $request->device_id))
            ->when($request->search, fn ($q) => $q->where('to_number', 'like', "%{$request->search}%"))
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $clients = Client::orderBy('name')->get(['id', 'name']);
        $devices = Device::orderBy('name')->get(['id', 'name']);

        return view('admin.sms.index', compact('messages', 'clients', 'devices'));
    }

    public function show(SmsMessage $smsMessage): View
    {
        $smsMessage->load(['client', 'device', 'apiKey', 'logs']);
        return view('admin.sms.show', compact('smsMessage'));
    }

    public function compose(): View
    {
        $clients = Client::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $devices = Device::where('status', '!=', 'disabled')->where('gateway_enabled', true)
            ->with('client')->orderBy('name')->get();

        return view('admin.sms.compose', compact('clients', 'devices'));
    }

    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'client_id'  => 'required|exists:clients,id',
            'device_id'  => 'nullable|exists:devices,id',
            'to'         => 'required|string|max:20',
            'message'    => 'required|string|max:1600',
            'priority'   => 'nullable|integer|min:1|max:10',
        ]);

        $client = Client::findOrFail($data['client_id']);
        $device = !empty($data['device_id']) ? Device::find($data['device_id']) : null;

        $result = $this->smsService->createMessage($data, $client, null, $device);

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['error']);
        }

        return redirect()->route('admin.sms.index')->with('success', 'SMS queued successfully.');
    }

    public function cancel(SmsMessage $smsMessage): RedirectResponse
    {
        if (!in_array($smsMessage->status, ['pending', 'reserved'])) {
            return back()->with('error', 'Cannot cancel a message in this state.');
        }

        $smsMessage->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        return back()->with('success', 'Message cancelled.');
    }

    public function retry(SmsMessage $smsMessage): RedirectResponse
    {
        if ($smsMessage->status !== 'failed') {
            return back()->with('error', 'Only failed messages can be retried.');
        }

        $smsMessage->update([
            'status'        => 'pending',
            'error_message' => null,
            'reserved_at'   => null,
            'failed_at'     => null,
        ]);

        return back()->with('success', 'Message reset to pending.');
    }
}
