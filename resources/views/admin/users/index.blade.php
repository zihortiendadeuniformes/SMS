@extends('layouts.app')
@section('title','Users')
@section('content')
<div class="flex justify-end mb-5">
    <a href="{{ route('admin.users.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> New User</a>
</div>
<div class="table-wrap">
<table class="data-table">
    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Created</th><th>Actions</th></tr></thead>
    <tbody>
    @forelse($users as $u)
    <tr>
        <td class="text-white font-medium">{{ $u->name }}</td>
        <td class="text-slate-400 text-sm">{{ $u->email }}</td>
        <td>@foreach($u->roles as $r)<span class="badge-reserved mr-1">{{ $r->name }}</span>@endforeach</td>
        <td class="text-xs text-slate-400">{{ $u->created_at->diffForHumans() }}</td>
        <td class="flex gap-1">
            <a href="{{ route('admin.users.edit',$u) }}" class="btn-secondary py-1 px-2 text-xs"><i class="fa-solid fa-pen"></i></a>
            @if($u->id !== auth()->id())<form method="POST" action="{{ route('admin.users.destroy',$u) }}" onsubmit="return confirm('Delete user?')">@csrf @method('DELETE')<button class="btn-danger py-1 px-2 text-xs"><i class="fa-solid fa-trash"></i></button></form>@endif
        </td>
    </tr>
    @empty
    <tr><td colspan="5" class="text-center text-slate-500 py-8">No users.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-4">{{ $users->withQueryString()->links() }}</div>
@endsection
