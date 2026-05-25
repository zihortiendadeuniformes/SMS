@extends('layouts.app')
@section('title','Settings')
@section('content')
<div class="max-w-2xl">
<div class="card">
    <h2 class="text-base font-semibold text-white mb-5"><i class="fa-solid fa-gear text-slate-400 mr-2"></i>Global Settings</h2>
    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
        @csrf
        @php
        $keys = [
            'default_heartbeat_interval_seconds' => ['Heartbeat Interval (sec)', 'integer'],
            'default_pull_interval_seconds'      => ['SMS Pull Interval (sec)', 'integer'],
            'offline_timeout_minutes'            => ['Offline Timeout (min)', 'integer'],
            'max_sms_per_minute'                 => ['Max SMS per Minute', 'integer'],
            'max_sms_per_day'                    => ['Max SMS per Day', 'integer'],
            'max_attempts'                       => ['Max Retry Attempts', 'integer'],
            'allow_remote_disable'               => ['Allow Remote Disable (true/false)', 'string'],
            'allow_remote_server_change'         => ['Allow Remote Server Change (true/false)', 'string'],
            'default_country_code'               => ['Default Country Code', 'string'],
            'admin_notification_email'           => ['Admin Email', 'string'],
        ];
        @endphp
        @foreach($keys as $key => [$label, $type])
        <div>
            <label class="form-label">{{ $label }}</label>
            <input
                name="settings[{{ $key }}]"
                value="{{ old("settings.$key", $settings[$key]?->value ?? '') }}"
                type="{{ $type === 'integer' ? 'text' : 'text' }}"
                class="form-input"
                placeholder="{{ $label }}">
            @if($settings[$key] ?? null)<p class="text-xs text-slate-500 mt-0.5">{{ $settings[$key]->description }}</p>@endif
        </div>
        @endforeach
        <button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Settings</button>
    </form>
</div>
</div>
@endsection
