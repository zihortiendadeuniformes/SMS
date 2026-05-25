<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $clients = Client::withCount(['devices', 'smsMessages', 'apiKeys'])
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderBy('name')
            ->paginate(20);

        return view('admin.clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('admin.clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'              => 'required|string|max:100',
            'company_name'      => 'nullable|string|max:150',
            'email'             => 'required|email|unique:clients',
            'phone'             => 'nullable|string|max:20',
            'status'            => 'required|in:active,inactive',
            'daily_sms_limit'   => 'required|integer|min:1',
            'monthly_sms_limit' => 'required|integer|min:1',
            'notes'             => 'nullable|string|max:1000',
        ]);

        Client::create($data);

        return redirect()->route('admin.clients.index')->with('success', 'Client created successfully.');
    }

    public function show(Client $client): View
    {
        $client->loadCount(['devices', 'smsMessages', 'apiKeys']);
        $recentMessages = $client->smsMessages()->with('device')->orderBy('created_at', 'desc')->limit(10)->get();
        $devices = $client->devices()->orderBy('name')->get();

        return view('admin.clients.show', compact('client', 'recentMessages', 'devices'));
    }

    public function edit(Client $client): View
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $data = $request->validate([
            'name'              => 'required|string|max:100',
            'company_name'      => 'nullable|string|max:150',
            'email'             => "required|email|unique:clients,email,{$client->id}",
            'phone'             => 'nullable|string|max:20',
            'status'            => 'required|in:active,inactive',
            'daily_sms_limit'   => 'required|integer|min:1',
            'monthly_sms_limit' => 'required|integer|min:1',
            'notes'             => 'nullable|string|max:1000',
        ]);

        $client->update($data);

        return redirect()->route('admin.clients.show', $client)->with('success', 'Client updated.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();
        return redirect()->route('admin.clients.index')->with('success', 'Client deleted.');
    }

    public function resetDailyUsage(Client $client): RedirectResponse
    {
        $client->update(['used_sms_today' => 0]);
        return back()->with('success', 'Daily usage reset.');
    }
}
