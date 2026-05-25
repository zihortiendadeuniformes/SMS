@extends('layouts.app')
@section('title','SMS #' . $smsMessage->id)
@section('content')
<div class="max-w-2xl space-y-5">
<div class="card">
    <div class="flex justify-between items-start mb-4">
        <h2 class="text-lg font-bold text-white">SMS #{{ $smsMessage->id }}</h2>
        <span class="badge-{{ $smsMessage->status }} text-sm px-3 py-1">{{ $smsMessage->status }}</span>
    </div>
    <dl class="grid grid-cols-2 gap-3 text-sm">
        <div><dt class="text-slate-400">To</dt><dd class="text-white font-mono">{{ $smsMessage->to_number }}</dd></div>
        <div><dt class="text-slate-400">From Device Number</dt><dd class="text-white font-mono">{{ $smsMessage->from_device_number ?? '—' }}</dd></div>
        <div><dt class="text-slate-400">Client</dt><dd class="text-white">{{ $smsMessage->client->name }}</dd></div>
        <div><dt class="text-slate-400">Device</dt><dd class="text-white">{{ $smsMessage->device?->name ?? '—' }}</dd></div>
        <div><dt class="text-slate-400">Priority</dt><dd class="text-white">{{ $smsMessage->priority }}</dd></div>
        <div><dt class="text-slate-400">Attempts</dt><dd class="text-white">{{ $smsMessage->attempts }} / {{ $smsMessage->max_attempts }}</dd></div>
        <div><dt class="text-slate-400">Created</dt><dd class="text-white">{{ $smsMessage->created_at->format('Y-m-d H:i:s') }}</dd></div>
        <div><dt class="text-slate-400">Sent At</dt><dd class="text-white">{{ $smsMessage->sent_at?->format('Y-m-d H:i:s') ?? '—' }}</dd></div>
        @if($smsMessage->error_message)
        <div class="col-span-2"><dt class="text-red-400">Error</dt><dd class="text-red-300 font-mono text-xs">{{ $smsMessage->error_message }}</dd></div>
        @endif
    </dl>
    <div class="mt-4 bg-slate-700 rounded-lg p-3">
        <p class="text-slate-400 text-xs mb-1">Message Body</p>
        <p class="text-white text-sm whitespace-pre-wrap">{{ $smsMessage->message_body }}</p>
    </div>
    <div class="flex gap-2 mt-5">
        @if(in_array($smsMessage->status,['pending','reserved']))<form method="POST" action="{{ route('admin.sms.cancel',$smsMessage) }}">@csrf<button class="btn-danger text-xs">Cancel</button></form>@endif
        @if($smsMessage->status==='failed')<form method="POST" action="{{ route('admin.sms.retry',$smsMessage) }}">@csrf<button class="btn-success text-xs">Retry</button></form>@endif
        <a href="{{ route('admin.sms.index') }}" class="btn-secondary text-xs">Back</a>
    </div>
</div>

@if($smsMessage->logs->isNotEmpty())
<div class="card">
    <h3 class="text-sm font-semibold text-slate-300 mb-3">Event Log</h3>
    @foreach($smsMessage->logs as $log)
    <div class="flex items-start gap-3 py-2 border-b border-slate-700 last:border-0">
        <span class="text-xs px-2 py-0.5 rounded {{ $log->level==='error'?'bg-red-900 text-red-300':($log->level==='warning'?'bg-yellow-900 text-yellow-300':'bg-slate-700 text-slate-300') }}">{{ $log->level }}</span>
        <div class="flex-1 text-xs"><div class="text-slate-200">{{ $log->message }}</div><div class="text-slate-500">{{ $log->created_at->format('H:i:s') }}</div></div>
    </div>
    @endforeach
</div>
@endif
</div>
@endsection
