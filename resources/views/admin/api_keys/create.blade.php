@extends('layouts.app')
@section('title','New API Key')
@section('content')
<div class="max-w-2xl">
<div class="card">
    <h2 class="text-base font-semibold text-white mb-5">Create API Key</h2>
    <form method="POST" action="{{ route('admin.api_keys.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div><label class="form-label">Client *</label><select name="client_id" required class="form-input"><option value="">Select client</option>@foreach($clients as $c)<option value="{{ $c->id }}" @selected(old('client_id')==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
            <div><label class="form-label">Key Name *</label><input name="name" value="{{ old('name') }}" required class="form-input" placeholder="Production Key"></div>
            <div><label class="form-label">Daily Limit</label><input type="number" name="daily_limit" value="{{ old('daily_limit',1000) }}" min="1" required class="form-input"></div>
            <div><label class="form-label">Monthly Limit</label><input type="number" name="monthly_limit" value="{{ old('monthly_limit',10000) }}" min="1" required class="form-input"></div>
        </div>
        <div><label class="form-label">Allowed IPs (comma-separated, leave blank for all)</label><input name="allowed_ips" value="{{ old('allowed_ips') }}" class="form-input" placeholder="192.168.1.1, 10.0.0.1"></div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="with_secret" id="with_secret" value="1" {{ old('with_secret') ? 'checked' : '' }}>
            <label for="with_secret" class="text-sm text-slate-300">Generate API Secret (optional extra security)</label>
        </div>
        <div class="flex gap-3">
            <button class="btn-primary">Create</button>
            <a href="{{ route('admin.api_keys.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</div>
@endsection
