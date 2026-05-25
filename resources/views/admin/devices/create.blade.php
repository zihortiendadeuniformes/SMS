@extends('layouts.app')
@section('title','New Device')
@section('content')
<div class="max-w-2xl">
<div class="card">
    <h2 class="text-base font-semibold text-white mb-5">Create Device</h2>
    <form method="POST" action="{{ route('admin.devices.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div><label class="form-label">Client *</label><select name="client_id" required class="form-input"><option value="">Select client</option>@foreach($clients as $c)<option value="{{ $c->id }}" @selected(old('client_id')==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
            <div><label class="form-label">Device Name *</label><input name="name" value="{{ old('name') }}" required class="form-input" placeholder="My Samsung Galaxy"></div>
            <div><label class="form-label">Phone Number</label><input name="phone_number" value="{{ old('phone_number') }}" class="form-input" placeholder="+18005550001"></div>
            <div><label class="form-label">Gateway Enabled</label><select name="gateway_enabled" class="form-input"><option value="1">Yes</option><option value="0">No</option></select></div>
            <div><label class="form-label">Heartbeat Interval (sec)</label><input type="number" name="heartbeat_interval_seconds" value="{{ old('heartbeat_interval_seconds',30) }}" min="10" max="300" required class="form-input"></div>
            <div><label class="form-label">Pull Interval (sec)</label><input type="number" name="pull_interval_seconds" value="{{ old('pull_interval_seconds',5) }}" min="3" max="60" required class="form-input"></div>
        </div>
        <div class="flex gap-3">
            <button class="btn-primary">Create Device</button>
            <a href="{{ route('admin.devices.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</div>
@endsection
