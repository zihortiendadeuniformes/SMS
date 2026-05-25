@extends('layouts.app')
@section('title', $apiKey->name)
@section('content')
<div class="max-w-2xl">
<div class="card">
    <div class="flex justify-between items-start mb-5">
        <div>
            <h2 class="text-lg font-bold text-white">{{ $apiKey->name }}</h2>
            <p class="text-slate-400 text-sm">{{ $apiKey->client->name }}</p>
        </div>
        <span class="badge-{{ $apiKey->status }}">{{ $apiKey->status }}</span>
    </div>

    @if(session('success'))
    <div class="bg-yellow-900/50 border border-yellow-600 rounded-lg p-4 mb-5">
        <p class="text-yellow-300 text-xs font-semibold mb-2">⚠ Save your API Key now — it won't be shown again in full.</p>
        <p class="text-xs text-slate-300 font-mono break-all">Key: <strong>{{ $apiKey->getRawOriginal('api_key') }}</strong></p>
        @if($apiKey->getRawOriginal('api_secret'))
        <p class="text-xs text-slate-300 font-mono break-all mt-1">Secret: <strong>{{ $apiKey->getRawOriginal('api_secret') }}</strong></p>
        @endif
    </div>
    @endif

    <div class="space-y-3 text-sm">
        <div class="flex justify-between"><span class="text-slate-400">API Key</span><span class="font-mono text-yellow-300">{{ substr($apiKey->getRawOriginal('api_key'),0,16).'…' }}</span></div>
        <div class="flex justify-between"><span class="text-slate-400">Daily Usage</span><span class="text-white">{{ $apiKey->used_today }} / {{ $apiKey->daily_limit }}</span></div>
        <div class="flex justify-between"><span class="text-slate-400">Monthly Usage</span><span class="text-white">{{ $apiKey->used_month }} / {{ $apiKey->monthly_limit }}</span></div>
        <div class="flex justify-between"><span class="text-slate-400">Allowed IPs</span><span class="text-white">{{ $apiKey->allowed_ips ?? 'All' }}</span></div>
        <div class="flex justify-between"><span class="text-slate-400">Last Used</span><span class="text-white">{{ $apiKey->last_used_at?->diffForHumans() ?? 'Never' }}</span></div>
    </div>

    <div class="flex gap-2 mt-6">
        <a href="{{ route('admin.api_keys.edit',$apiKey) }}" class="btn-secondary text-xs">Edit</a>
        <form method="POST" action="{{ route('admin.api_keys.regenerate',$apiKey) }}" onsubmit="return confirm('Regenerate?')">@csrf<button class="btn-secondary text-xs">Regenerate Key</button></form>
        <form method="POST" action="{{ route('admin.api_keys.destroy',$apiKey) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn-danger text-xs">Delete</button></form>
    </div>
</div>
</div>
@endsection
