<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedNumber;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlockedNumberController extends Controller
{
    public function index(Request $request): View
    {
        try {
            $blocked = BlockedNumber::with('client')
                ->when($request->search, fn ($q) => $q->where('phone_number', 'like', "%{$request->search}%"))
                ->when($request->client_id, fn ($q) => $q->where('client_id', $request->client_id))
                ->orderBy('created_at', 'desc')
                ->paginate(30);
            $clients = Client::orderBy('name')->get(['id', 'name']);
        } catch (\Throwable $e) {
            $blocked = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 30);
            $clients = collect();
        }

        return view('admin.blocked_numbers.index', compact('blocked', 'clients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'phone_number' => 'required|string|max:20',
            'client_id'    => 'nullable|exists:clients,id',
            'reason'       => 'nullable|string|max:200',
        ]);

        BlockedNumber::firstOrCreate(
            ['phone_number' => $data['phone_number'], 'client_id' => $data['client_id'] ?? null],
            ['reason' => $data['reason'] ?? null]
        );

        return back()->with('success', 'Number blocked.');
    }

    public function destroy(BlockedNumber $blockedNumber): RedirectResponse
    {
        $blockedNumber->delete();
        return back()->with('success', 'Number unblocked.');
    }
}
