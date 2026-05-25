@extends('layouts.app')
@section('title','API Keys')
@section('content')
<div class="flex items-center justify-between mb-5">
    <form class="flex gap-2">
        <select name="client_id" class="form-input w-52"><option value="">All clients</option>@foreach($clients as $c)<option value="{{ $c->id }}" @selected(request('client_id')==$c->id)>{{ $c->name }}</option>@endforeach</select>
        <select name="status" class="form-input w-36"><option value="">All status</option><option value="active">Active</option><option value="inactive">Inactive</option></select>
        <button class="btn-secondary">Filter</button>
    </form>
    <a href="{{ route('admin.api_keys.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> New API Key</a>
</div>
<div class="table-wrap">
<table class="data-table">
    <thead><tr><th>Name</th><th>Client</th><th>Status</th><th>Used Today</th><th>Used Month</th><th>Last Used</th><th>Actions</th></tr></thead>
    <tbody>
    @forelse($apiKeys as $k)
    <tr>
        <td><a href="{{ route('admin.api_keys.show',$k) }}" class="text-blue-400 hover:underline font-medium">{{ $k->name }}</a></td>
        <td class="text-slate-400 text-xs">{{ $k->client->name }}</td>
        <td><span class="badge-{{ $k->status }}">{{ $k->status }}</span></td>
        <td>{{ $k->used_today }} / {{ $k->daily_limit }}</td>
        <td>{{ $k->used_month }} / {{ $k->monthly_limit }}</td>
        <td class="text-xs text-slate-400">{{ $k->last_used_at?->diffForHumans() ?? 'Never' }}</td>
        <td class="flex gap-1">
            <a href="{{ route('admin.api_keys.edit',$k) }}" class="btn-secondary py-1 px-2 text-xs"><i class="fa-solid fa-pen"></i></a>
            <form method="POST" action="{{ route('admin.api_keys.regenerate',$k) }}" onsubmit="return confirm('Regenerate?')">@csrf<button class="btn-secondary py-1 px-2 text-xs"><i class="fa-solid fa-rotate"></i></button></form>
            <form method="POST" action="{{ route('admin.api_keys.destroy',$k) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn-danger py-1 px-2 text-xs"><i class="fa-solid fa-trash"></i></button></form>
        </td>
    </tr>
    @empty
    <tr><td colspan="7" class="text-center text-slate-500 py-8">No API keys found.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-4">{{ $apiKeys->withQueryString()->links() }}</div>
@endsection
