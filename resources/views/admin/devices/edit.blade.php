@extends('layouts.app')
@section('title','Edit Device')
@section('content')
<div class="max-w-2xl">
<div class="card">
    <h2 class="text-base font-semibold text-white mb-5">Edit Device — {{ $device->name }}</h2>
    <form method="POST" action="{{ route('admin.devices.update',$device) }}" class="space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div><label class="form-label">Device Name *</label><input name="name" value="{{ old('name',$device->name) }}" required class="form-input"></div>
            <div><label class="form-label">Phone Number</label><input name="phone_number" value="{{ old('phone_number',$device->phone_number) }}" class="form-input"></div>
            <div><label class="form-label">Heartbeat Interval (sec)</label><input type="number" name="heartbeat_interval_seconds" value="{{ old('heartbeat_interval_seconds',$device->heartbeat_interval_seconds) }}" min="10" max="300" required class="form-input"></div>
            <div><label class="form-label">Pull Interval (sec)</label><input type="number" name="pull_interval_seconds" value="{{ old('pull_interval_seconds',$device->pull_interval_seconds) }}" min="3" max="60" required class="form-input"></div>
        </div>
        <div class="flex gap-3">
            <button class="btn-primary">Save</button>
            <a href="{{ route('admin.devices.show',$device) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</div>
@endsection
