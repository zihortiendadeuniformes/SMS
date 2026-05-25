@extends('layouts.app')
@section('title','New Client')
@section('content')
<div class="max-w-2xl">
<div class="card">
    <h2 class="text-base font-semibold text-white mb-5">Create Client</h2>
    <form method="POST" action="{{ route('admin.clients.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div><label class="form-label">Name *</label><input name="name" value="{{ old('name') }}" required class="form-input"></div>
            <div><label class="form-label">Company</label><input name="company_name" value="{{ old('company_name') }}" class="form-input"></div>
            <div><label class="form-label">Email *</label><input type="email" name="email" value="{{ old('email') }}" required class="form-input"></div>
            <div><label class="form-label">Phone</label><input name="phone" value="{{ old('phone') }}" class="form-input"></div>
            <div><label class="form-label">Status</label><select name="status" class="form-input"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <div></div>
            <div><label class="form-label">Daily SMS Limit</label><input type="number" name="daily_sms_limit" value="{{ old('daily_sms_limit',1000) }}" min="1" required class="form-input"></div>
            <div><label class="form-label">Monthly SMS Limit</label><input type="number" name="monthly_sms_limit" value="{{ old('monthly_sms_limit',10000) }}" min="1" required class="form-input"></div>
        </div>
        <div><label class="form-label">Notes</label><textarea name="notes" rows="3" class="form-input">{{ old('notes') }}</textarea></div>
        <div class="flex gap-3">
            <button class="btn-primary">Create</button>
            <a href="{{ route('admin.clients.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</div>
@endsection
