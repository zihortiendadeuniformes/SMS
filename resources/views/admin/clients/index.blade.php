@extends('layouts.app')
@section('title','Clients')
@section('content')
<div class="flex items-center justify-between mb-5">
    <form class="flex gap-2">
        <input name="search" value="{{ request('search') }}" placeholder="Search…" class="form-input w-64">
        <select name="status" class="form-input w-36"><option value="">All status</option><option value="active" @selected(request('status')=='active')>Active</option><option value="inactive" @selected(request('status')=='inactive')>Inactive</option></select>
        <button class="btn-secondary">Filter</button>
    </form>
    <a href="{{ route('admin.clients.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> New Client</a>
</div>
<div class="table-wrap">
<table class="data-table">
    <thead><tr><th>Name</th><th>Email</th><th>Status</th><th>Devices</th><th>API Keys</th><th>SMS Today/Daily</th><th>Actions</th></tr></thead>
    <tbody>
    @forelse($clients as $c)
    <tr>
        <td><a href="{{ route('admin.clients.show',$c) }}" class="text-blue-400 hover:underline font-medium">{{ $c->name }}</a><div class="text-xs text-slate-500">{{ $c->company_name }}</div></td>
        <td>{{ $c->email }}</td>
        <td><span class="badge-{{ $c->status }}">{{ $c->status }}</span></td>
        <td>{{ $c->devices_count }}</td>
        <td>{{ $c->api_keys_count }}</td>
        <td>{{ $c->used_sms_today }} / {{ $c->daily_sms_limit }}</td>
        <td class="flex gap-2">
            <a href="{{ route('admin.clients.edit',$c) }}" class="btn-secondary py-1 px-2 text-xs"><i class="fa-solid fa-pen"></i></a>
            <form method="POST" action="{{ route('admin.clients.destroy',$c) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn-danger py-1 px-2 text-xs"><i class="fa-solid fa-trash"></i></button></form>
        </td>
    </tr>
    @empty
    <tr><td colspan="7" class="text-center text-slate-500 py-8">No clients found.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-4">{{ $clients->withQueryString()->links() }}</div>
@endsection
