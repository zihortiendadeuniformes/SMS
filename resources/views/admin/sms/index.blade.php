@extends('layouts.app')
@section('title','SMS Messages')
@section('content')
<div class="flex items-center justify-between mb-5">
    <form class="flex flex-wrap gap-2">
        <input name="search" value="{{ request('search') }}" placeholder="Search number…" class="form-input w-44">
        <select name="status" class="form-input w-36"><option value="">All status</option>@foreach(['pending','reserved','sending','sent','failed','cancelled'] as $s)<option value="{{ $s }}" @selected(request('status')==$s)>{{ ucfirst($s) }}</option>@endforeach</select>
        <select name="client_id" class="form-input w-44"><option value="">All clients</option>@foreach($clients as $c)<option value="{{ $c->id }}" @selected(request('client_id')==$c->id)>{{ $c->name }}</option>@endforeach</select>
        <button class="btn-secondary">Filter</button>
    </form>
    <a href="{{ route('admin.sms.compose') }}" class="btn-primary"><i class="fa-solid fa-paper-plane"></i> Send SMS</a>
</div>
<div class="table-wrap">
<table class="data-table">
    <thead><tr><th>To</th><th>Message</th><th>Client</th><th>Device</th><th>Status</th><th>Attempts</th><th>Created</th><th>Actions</th></tr></thead>
    <tbody>
    @forelse($messages as $m)
    <tr>
        <td class="font-mono text-sm">{{ $m->to_number }}</td>
        <td class="text-slate-400 text-xs max-w-[200px] truncate">{{ $m->message_body }}</td>
        <td class="text-xs text-slate-400">{{ $m->client->name }}</td>
        <td class="text-xs text-slate-400">{{ $m->device?->name ?? '—' }}</td>
        <td><span class="badge-{{ $m->status }}">{{ $m->status }}</span></td>
        <td class="text-xs text-center">{{ $m->attempts }}/{{ $m->max_attempts }}</td>
        <td class="text-xs text-slate-400">{{ $m->created_at->diffForHumans() }}</td>
        <td class="flex gap-1">
            <a href="{{ route('admin.sms.show',$m) }}" class="btn-secondary py-1 px-2 text-xs"><i class="fa-solid fa-eye"></i></a>
            @if(in_array($m->status,['pending','reserved']))<form method="POST" action="{{ route('admin.sms.cancel',$m) }}">@csrf<button class="btn-danger py-1 px-2 text-xs">Cancel</button></form>@endif
            @if($m->status==='failed')<form method="POST" action="{{ route('admin.sms.retry',$m) }}">@csrf<button class="btn-success py-1 px-2 text-xs">Retry</button></form>@endif
        </td>
    </tr>
    @empty
    <tr><td colspan="8" class="text-center text-slate-500 py-8">No messages found.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-4">{{ $messages->withQueryString()->links() }}</div>
@endsection
