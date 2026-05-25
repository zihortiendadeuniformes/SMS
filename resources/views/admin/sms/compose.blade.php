@extends('layouts.app')
@section('title','Send SMS')
@section('content')
<div class="max-w-xl">
<div class="card">
    <h2 class="text-base font-semibold text-white mb-5"><i class="fa-solid fa-paper-plane text-blue-400 mr-2"></i>Send Manual SMS</h2>
    <form method="POST" action="{{ route('admin.sms.send') }}" class="space-y-4">
        @csrf
        <div>
            <label class="form-label">Client *</label>
            <select name="client_id" required class="form-input">
                <option value="">Select client</option>
                @foreach($clients as $c)<option value="{{ $c->id }}" @selected(old('client_id')==$c->id)>{{ $c->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Device (optional — leave blank for auto-assign)</label>
            <select name="device_id" class="form-input">
                <option value="">Auto (any available)</option>
                @foreach($devices as $d)
                <option value="{{ $d->id }}" @selected(old('device_id')==$d->id)>{{ $d->name }} ({{ $d->client->name }}) — {{ $d->status }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Destination Number *</label>
            <input name="to" value="{{ old('to') }}" required class="form-input" placeholder="+18005551234">
        </div>
        <div>
            <label class="form-label">Message *</label>
            <textarea name="message" rows="4" required maxlength="1600" class="form-input" placeholder="Your message here…">{{ old('message') }}</textarea>
            <p class="text-xs text-slate-500 mt-1">Max 1600 characters</p>
        </div>
        <div>
            <label class="form-label">Priority (1=High, 10=Low)</label>
            <input type="number" name="priority" value="{{ old('priority',5) }}" min="1" max="10" class="form-input w-24">
        </div>
        <div class="flex gap-3">
            <button class="btn-primary"><i class="fa-solid fa-paper-plane"></i> Send</button>
            <a href="{{ route('admin.sms.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</div>
@endsection
