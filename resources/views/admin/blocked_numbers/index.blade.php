@extends('layouts.app')
@section('title','Blocked Numbers')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
<div class="card">
    <h3 class="text-sm font-semibold text-slate-300 mb-4">Block a Number</h3>
    <form method="POST" action="{{ route('admin.blocked_numbers.store') }}" class="space-y-3">
        @csrf
        <div><label class="form-label">Phone Number *</label><input name="phone_number" required class="form-input" placeholder="+18005551234"></div>
        <div><label class="form-label">Client (blank = global)</label><select name="client_id" class="form-input"><option value="">Global (all clients)</option>@foreach($clients as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
        <div><label class="form-label">Reason</label><input name="reason" class="form-input" placeholder="Opt-out, spam…"></div>
        <button class="btn-danger w-full"><i class="fa-solid fa-ban"></i> Block Number</button>
    </form>
</div>
<div class="lg:col-span-2">
    <div class="flex gap-2 mb-4">
        <form class="flex gap-2 flex-1">
            <input name="search" value="{{ request('search') }}" placeholder="Search number…" class="form-input flex-1">
            <select name="client_id" class="form-input w-44"><option value="">All clients</option>@foreach($clients as $c)<option value="{{ $c->id }}" @selected(request('client_id')==$c->id)>{{ $c->name }}</option>@endforeach</select>
            <button class="btn-secondary">Filter</button>
        </form>
    </div>
    <div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Number</th><th>Scope</th><th>Reason</th><th>Blocked At</th><th></th></tr></thead>
        <tbody>
        @forelse($blocked as $b)
        <tr>
            <td class="font-mono text-white">{{ $b->phone_number }}</td>
            <td class="text-xs text-slate-400">{{ $b->client?->name ?? 'Global' }}</td>
            <td class="text-xs text-slate-400">{{ $b->reason ?? '—' }}</td>
            <td class="text-xs text-slate-400">{{ $b->created_at->diffForHumans() }}</td>
            <td><form method="POST" action="{{ route('admin.blocked_numbers.destroy',$b) }}">@csrf @method('DELETE')<button class="btn-danger py-1 px-2 text-xs">Unblock</button></form></td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-slate-500 py-8">No blocked numbers.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-4">{{ $blocked->withQueryString()->links() }}</div>
</div>
</div>
@endsection
