<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiKeyController extends Controller
{
    public function index(Request $request): View
    {
        $apiKeys = ApiKey::with('client')
            ->when($request->client_id, fn ($q) => $q->where('client_id', $request->client_id))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $clients = Client::orderBy('name')->get(['id', 'name']);

        return view('admin.api_keys.index', compact('apiKeys', 'clients'));
    }

    public function create(): View
    {
        $clients = Client::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        return view('admin.api_keys.create', compact('clients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'client_id'     => 'required|exists:clients,id',
            'name'          => 'required|string|max:100',
            'allowed_ips'   => 'nullable|string|max:500',
            'daily_limit'   => 'required|integer|min:1',
            'monthly_limit' => 'required|integer|min:1',
            'with_secret'   => 'nullable|boolean',
        ]);

        $key = ApiKey::create([
            'client_id'     => $data['client_id'],
            'name'          => $data['name'],
            'api_key'       => ApiKey::generateKey(),
            'api_secret'    => !empty($data['with_secret']) ? ApiKey::generateSecret() : null,
            'allowed_ips'   => $data['allowed_ips'] ?? null,
            'daily_limit'   => $data['daily_limit'],
            'monthly_limit' => $data['monthly_limit'],
            'status'        => 'active',
        ]);

        return redirect()->route('admin.api_keys.show', $key)
            ->with('success', 'API Key created. Save the key now, it will be hidden after.');
    }

    public function show(ApiKey $apiKey): View
    {
        $apiKey->load('client');
        $rawKey = session('_flash.success') ? $apiKey->getRawOriginal('api_key') : null;
        return view('admin.api_keys.show', compact('apiKey', 'rawKey'));
    }

    public function edit(ApiKey $apiKey): View
    {
        return view('admin.api_keys.edit', compact('apiKey'));
    }

    public function update(Request $request, ApiKey $apiKey): RedirectResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'allowed_ips'   => 'nullable|string|max:500',
            'daily_limit'   => 'required|integer|min:1',
            'monthly_limit' => 'required|integer|min:1',
            'status'        => 'required|in:active,inactive',
        ]);

        $apiKey->update($data);

        return redirect()->route('admin.api_keys.show', $apiKey)->with('success', 'API Key updated.');
    }

    public function destroy(ApiKey $apiKey): RedirectResponse
    {
        $apiKey->delete();
        return redirect()->route('admin.api_keys.index')->with('success', 'API Key deleted.');
    }

    public function regenerate(ApiKey $apiKey): RedirectResponse
    {
        $apiKey->update(['api_key' => ApiKey::generateKey()]);
        return back()->with('success', 'API Key regenerated: ' . $apiKey->getRawOriginal('api_key'));
    }
}
