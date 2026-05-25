@extends('layouts.app')
@section('title','Edit User')
@section('content')
<div class="max-w-lg">
<div class="card">
    <h2 class="text-base font-semibold text-white mb-5">Edit User — {{ $user->name }}</h2>
    <form method="POST" action="{{ route('admin.users.update',$user) }}" class="space-y-4">
        @csrf @method('PUT')
        <div><label class="form-label">Name *</label><input name="name" value="{{ old('name',$user->name) }}" required class="form-input"></div>
        <div><label class="form-label">Email *</label><input type="email" name="email" value="{{ old('email',$user->email) }}" required class="form-input"></div>
        <div><label class="form-label">New Password (leave blank to keep)</label><input type="password" name="password" minlength="8" class="form-input"></div>
        <div><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-input"></div>
        <div><label class="form-label">Role *</label><select name="role" required class="form-input">@foreach($roles as $r)<option value="{{ $r->name }}" @selected($user->hasRole($r->name))>{{ $r->name }}</option>@endforeach</select></div>
        <div class="flex gap-3">
            <button class="btn-primary">Save</button>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</div>
@endsection
