@extends('layouts.app')
@section('title','Logs')
@section('content')
<form class="flex flex-wrap gap-2 mb-5">
    <select name="level" class="form-input w-32"><option value="">All levels</option><option value="info" @selected(request('level')=='info')>Info</option><option value="warning" @selected(request('level')=='warning')>Warning</option><option value="error" @selected(request('level')=='error')>Error</option></select>
    <select name="type" class="form-input w-52"><option value="">All types</option>@foreach($logTypes as $t)<option value="{{ $t }}" @selected(request('type')==$t)>{{ $t }}</option>@endforeach</select>
    <select name="device_id" class="form-input w-44"><option value="">All devices</option>@foreach($devices as $d)<option value="{{ $d->id }}" @selected(request('device_id')==$d->id)>{{ $d->name }}</option>@endforeach</select>
    <select name="client_id" class="form-input w-44"><option value="">All clients</option>@foreach($clients as $c)<option value="{{ $c->id }}" @selected(request('client_id')==$c->id)>{{ $c->name }}</option>@endforeach</select>
    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input w-36">
    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input w-36">
    <button class="btn-secondary">Filter</button>
</form>
<div class="table-wrap">
<table class="data-table">
    <thead><tr><th>Time</th><th>Level</th><th>Type</th><th>Message</th><th>Device</th><th>Client</th></tr></thead>
    <tbody>
    @forelse($logs as $log)
    <tr>
        <td class="text-xs text-slate-400 whitespace-nowrap">{{ $log->created_at->format('m-d H:i:s') }}</td>
        <td><span class="text-xs px-2 py-0.5 rounded font-semibold {{ $log->level==='error'?'bg-red-900 text-red-300':($log->level==='warning'?'bg-yellow-900 text-yellow-300':'bg-slate-700 text-slate-300') }}">{{ $log->level }}</span></td>
        <td class="text-xs font-mono text-slate-300">{{ $log->type }}</td>
        <td class="text-xs text-slate-200 max-w-xs truncate">{{ $log->message }}</td>
        <td class="text-xs text-slate-400">{{ $log->device?->name ?? '—' }}</td>
        <td class="text-xs text-slate-400">{{ $log->client?->name ?? '—' }}</td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center text-slate-500 py-8">No logs found.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-4">{{ $logs->withQueryString()->links() }}</div>
@endsection
