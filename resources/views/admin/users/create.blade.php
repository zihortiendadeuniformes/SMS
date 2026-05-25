@extends('layouts.app')
@section('title','New User')
@section('content')
<div class="max-w-lg">
<div class="card">
    <h2 class="text-base font-semibold text-white mb-5">Create User</h2>
    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
        @csrf
        <div><label class="form-label">Name *</label><input name="name" value="{{ old('name') }}" required class="form-input"></div>
        <div><label class="form-label">Email *</label><input type="email" name="email" value="{{ old('email') }}" required class="form-input"></div>
        <div><label class="form-label">Password *</label><input type="password" name="password" required minlength="8" class="form-input"></div>
        <div><label class="form-label">Confirm Password *</label><input type="password" name="password_confirmation" required class="form-input"></div>
        <div><label class="form-label">Role *</label><select name="role" required class="form-input"><option value="">Select role</option>@foreach($roles as $r)<option value="{{ $r->name }}" @selected(old('role')==$r->name)>{{ $r->name }}</option>@endforeach</select></div>
        <div class="flex gap-3">
            <button class="btn-primary">Create</button>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</div>
@endsection
