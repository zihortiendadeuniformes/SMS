@extends('layouts.app')
@section('title','Edit API Key')
@section('content')
<div class="max-w-2xl">
<div class="card">
    <h2 class="text-base font-semibold text-white mb-5">Edit API Key — {{ $apiKey->name }}</h2>
    <form method="POST" action="{{ route('admin.api_keys.update',$apiKey) }}" class="space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div><label class="form-label">Name *</label><input name="name" value="{{ old('name',$apiKey->name) }}" required class="form-input"></div>
            <div><label class="form-label">Status</label><select name="status" class="form-input"><option value="active" @selected($apiKey->status=='active')>Active</option><option value="inactive" @selected($apiKey->status=='inactive')>Inactive</option></select></div>
            <div><label class="form-label">Daily Limit</label><input type="number" name="daily_limit" value="{{ old('daily_limit',$apiKey->daily_limit) }}" min="1" required class="form-input"></div>
            <div><label class="form-label">Monthly Limit</label><input type="number" name="monthly_limit" value="{{ old('monthly_limit',$apiKey->monthly_limit) }}" min="1" required class="form-input"></div>
        </div>
        <div><label class="form-label">Allowed IPs</label><input name="allowed_ips" value="{{ old('allowed_ips',$apiKey->allowed_ips) }}" class="form-input"></div>
        <div class="flex gap-3">
            <button class="btn-primary">Save</button>
            <a href="{{ route('admin.api_keys.show',$apiKey) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</div>
@endsection
