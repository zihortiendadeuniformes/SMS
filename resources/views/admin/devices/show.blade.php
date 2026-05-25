@extends('layouts.app')
@section('title', $device->name)
@section('content')
<div class="flex flex-wrap items-start gap-4 mb-6">
    <div class="flex-1">
        <div class="flex items-center gap-3 mb-1">
            <h2 class="text-xl font-bold text-white">{{ $device->name }}</h2>
            <span class="badge-{{ $device->status }}">{{ $device->status }}</span>
            @if($device->gateway_enabled)<span class="badge-active">Gateway ON</span>@else<span class="badge-inactive">Gateway OFF</span>@endif
        </div>
        <p class="text-slate-400 text-sm">Client: {{ $device->client->name }} &bull; Phone: {{ $device->phone_number ?? '—' }}</p>
        <p class="text-slate-500 text-xs mt-1">Pairing Code: <span class="font-mono text-yellow-300 font-bold">{{ $device->pairing_code ?? 'Used' }}</span></p>
    </div>
    <div class="flex flex-wrap gap-2">
        <form method="POST" action="{{ route('admin.devices.toggle-gateway',$device) }}">@csrf<button class="{{ $device->gateway_enabled ? 'btn-danger' : 'btn-success' }} text-xs py-1.5">{{ $device->gateway_enabled ? 'Disable Gateway' : 'Enable Gateway' }}</button></form>
        <form method="POST" action="{{ route('admin.devices.toggle-status',$device) }}">@csrf<button class="{{ $device->status==='disabled' ? 'btn-success' : 'btn-secondary' }} text-xs py-1.5">{{ $device->status==='disabled' ? 'Enable Device' : 'Disable Device' }}</button></form>
        <form method="POST" action="{{ route('admin.devices.regenerate-token',$device) }}" onsubmit="return confirm('Regenerate token? The app will be disconnected.')">@csrf<button class="btn-secondary text-xs py-1.5"><i class="fa-solid fa-rotate"></i> Regen Token</button></form>
        <form method="POST" action="{{ route('admin.devices.regenerate-pairing',$device) }}" onsubmit="return confirm('Regenerate pairing code? Old token will be revoked.')">@csrf<button class="btn-secondary text-xs py-1.5"><i class="fa-solid fa-qrcode"></i> Regen Pairing</button></form>
        <a href="{{ route('admin.devices.edit',$device) }}" class="btn-secondary text-xs py-1.5"><i class="fa-solid fa-pen"></i> Edit</a>
    </div>
</div>

{{-- Info Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([['Battery',$device->battery_level!==null?$device->battery_level.'%':'—','fa-battery-half','text-green-400'],['Signal',$device->signal_strength!==null?$device->signal_strength.'%':'—','fa-signal','text-blue-400'],['Operator',$device->sim_operator??'—','fa-sim-card','text-purple-400'],['App Version',$device->app_version??'—','fa-mobile','text-yellow-400']] as [$lbl,$val,$ic,$col])
    <div class="card flex items-center gap-3"><i class="fa-solid {{ $ic }} {{ $col }}"></i><div><div class="text-lg font-bold text-white">{{ $val }}</div><div class="text-xs text-slate-400">{{ $lbl }}</div></div></div>
    @endforeach
</div>

{{-- Send Command --}}
<div class="card mb-6">
    <h3 class="text-sm font-semibold text-slate-300 mb-3">Send Remote Command</h3>
    <form method="POST" action="{{ route('admin.devices.send-command',$device) }}" class="flex gap-3 flex-wrap">
        @csrf
        <select name="command" class="form-input w-60">
            @foreach(\App\Models\DeviceCommand::AVAILABLE_COMMANDS as $cmd)
            <option value="{{ $cmd }}">{{ $cmd }}</option>
            @endforeach
        </select>
        <button class="btn-primary"><i class="fa-solid fa-terminal"></i> Send Command</button>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card">
        <h3 class="text-sm font-semibold text-slate-300 mb-3">Recent Messages</h3>
        @forelse($recentMessages as $m)
        <div class="flex justify-between py-2 border-b border-slate-700 last:border-0 text-sm">
            <span class="font-mono text-white">{{ $m->to_number }}</span>
            <span class="badge-{{ $m->status }}">{{ $m->status }}</span>
        </div>
        @empty<p class="text-sm text-slate-500">No messages.</p>@endforelse
    </div>
    <div class="card">
        <h3 class="text-sm font-semibold text-slate-300 mb-3">Pending Commands</h3>
        @forelse($pendingCommands as $cmd)
        <div class="flex justify-between py-2 border-b border-slate-700 last:border-0 text-sm">
            <span class="text-yellow-300 font-mono">{{ $cmd->command }}</span>
            <span class="text-slate-400 text-xs">{{ $cmd->created_at->diffForHumans() }}</span>
        </div>
        @empty<p class="text-sm text-slate-500">No pending commands.</p>@endforelse
    </div>
</div>
@endsection
