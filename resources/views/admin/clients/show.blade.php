@extends('layouts.app')
@section('title', $client->name)
@section('content')
<div class="flex items-center gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-white">{{ $client->name }}</h2>
        <p class="text-slate-400 text-sm">{{ $client->email }}</p>
    </div>
    <span class="badge-{{ $client->status }}">{{ $client->status }}</span>
    <div class="ml-auto flex gap-2">
        <a href="{{ route('admin.clients.edit',$client) }}" class="btn-secondary"><i class="fa-solid fa-pen"></i> Edit</a>
        <form method="POST" action="{{ route('admin.clients.destroy',$client) }}" onsubmit="return confirm('Delete client and all data?')">@csrf @method('DELETE')<button class="btn-danger"><i class="fa-solid fa-trash"></i></button></form>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([['Devices',$client->devices_count,'fa-mobile-screen','text-purple-400'],['API Keys',$client->api_keys_count,'fa-key','text-yellow-400'],['SMS Today',$client->used_sms_today,'fa-comment-sms','text-blue-400'],['SMS Month',$client->used_sms_month,'fa-calendar','text-green-400']] as [$label,$val,$icon,$color])
    <div class="card flex items-center gap-3">
        <i class="fa-solid {{ $icon }} {{ $color }} text-xl"></i>
        <div><div class="text-xl font-bold text-white">{{ $val }}</div><div class="text-xs text-slate-400">{{ $label }}</div></div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card">
        <h3 class="text-sm font-semibold text-slate-300 mb-3">Devices</h3>
        @forelse($devices as $d)
        <div class="flex justify-between items-center py-2 border-b border-slate-700 last:border-0">
            <a href="{{ route('admin.devices.show',$d) }}" class="text-blue-400 hover:underline text-sm">{{ $d->name }}</a>
            <span class="badge-{{ $d->status }}">{{ $d->status }}</span>
        </div>
        @empty<p class="text-sm text-slate-500">No devices.</p>@endforelse
    </div>
    <div class="card">
        <h3 class="text-sm font-semibold text-slate-300 mb-3">Recent Messages</h3>
        @forelse($recentMessages as $m)
        <div class="flex justify-between items-center py-2 border-b border-slate-700 last:border-0 text-sm">
            <span class="text-white font-mono">{{ $m->to_number }}</span>
            <span class="badge-{{ $m->status }}">{{ $m->status }}</span>
        </div>
        @empty<p class="text-sm text-slate-500">No messages.</p>@endforelse
    </div>
</div>
@endsection
