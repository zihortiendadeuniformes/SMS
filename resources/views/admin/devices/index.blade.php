@extends('layouts.app')
@section('title','Devices')
@section('content')
<div class="flex items-center justify-between mb-5">
    <form class="flex gap-2 flex-wrap">
        <input name="search" value="{{ request('search') }}" placeholder="Search…" class="form-input w-48">
        <select name="status" class="form-input w-36"><option value="">All status</option><option value="online" @selected(request('status')=='online')>Online</option><option value="offline" @selected(request('status')=='offline')>Offline</option><option value="disabled" @selected(request('status')=='disabled')>Disabled</option></select>
        <select name="client_id" class="form-input w-48"><option value="">All clients</option>@foreach($clients as $c)<option value="{{ $c->id }}" @selected(request('client_id')==$c->id)>{{ $c->name }}</option>@endforeach</select>
        <button class="btn-secondary">Filter</button>
    </form>
    <a href="{{ route('admin.devices.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> New Device</a>
</div>
<div class="table-wrap">
<table class="data-table">
    <thead><tr><th>Device</th><th>Client</th><th>Phone</th><th>Status</th><th>Gateway</th><th>Battery</th><th>Last Seen</th><th>Actions</th></tr></thead>
    <tbody>
    @forelse($devices as $d)
    <tr>
        <td><a href="{{ route('admin.devices.show',$d) }}" class="text-blue-400 hover:underline font-medium">{{ $d->name }}</a></td>
        <td class="text-slate-400 text-xs">{{ $d->client->name }}</td>
        <td class="font-mono text-xs">{{ $d->phone_number ?? '—' }}</td>
        <td><span class="badge-{{ $d->status }}">{{ $d->status }}</span></td>
        <td>
            @if($d->gateway_enabled)
                <span class="badge-active"><i class="fa-solid fa-circle text-xs mr-1"></i>ON</span>
            @else
                <span class="badge-inactive"><i class="fa-solid fa-circle text-xs mr-1"></i>OFF</span>
            @endif
        </td>
        <td>{{ $d->battery_level !== null ? $d->battery_level.'%' : '—' }}</td>
        <td class="text-xs text-slate-400">{{ $d->last_seen_at?->diffForHumans() ?? 'Never' }}</td>
        <td class="flex gap-1">
            <a href="{{ route('admin.devices.show',$d) }}" class="btn-secondary py-1 px-2 text-xs"><i class="fa-solid fa-eye"></i></a>
            <a href="{{ route('admin.devices.edit',$d) }}" class="btn-secondary py-1 px-2 text-xs"><i class="fa-solid fa-pen"></i></a>
        </td>
    </tr>
    @empty
    <tr><td colspan="8" class="text-center text-slate-500 py-8">No devices found.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-4">{{ $devices->withQueryString()->links() }}</div>
@endsection
