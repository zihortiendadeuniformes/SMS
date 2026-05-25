@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@php
$cards = [
    ['label'=>'Total Clients',  'value'=>$stats['total_clients'],    'icon'=>'fa-users',          'color'=>'#60a5fa', 'bg'=>'#0c1e40'],
    ['label'=>'Total Devices',  'value'=>$stats['total_devices'],    'icon'=>'fa-mobile-screen',  'color'=>'#a78bfa', 'bg'=>'#160c33'],
    ['label'=>'Online',         'value'=>$stats['devices_online'],   'icon'=>'fa-circle',         'color'=>'#4ade80', 'bg'=>'#052e16'],
    ['label'=>'Offline',        'value'=>$stats['devices_offline'],  'icon'=>'fa-circle',         'color'=>'#94a3b8', 'bg'=>'#111e2f'],
    ['label'=>'Disabled',       'value'=>$stats['devices_disabled'], 'icon'=>'fa-ban',            'color'=>'#f87171', 'bg'=>'#3f0f0f'],
    ['label'=>'SMS Pending',    'value'=>$stats['sms_pending'],      'icon'=>'fa-hourglass-half', 'color'=>'#fbbf24', 'bg'=>'#3f2900'],
    ['label'=>'SMS Reserved',   'value'=>$stats['sms_reserved'],     'icon'=>'fa-lock',           'color'=>'#60a5fa', 'bg'=>'#0c1e40'],
    ['label'=>'Sent Today',     'value'=>$stats['sms_sent_today'],   'icon'=>'fa-check-double',   'color'=>'#4ade80', 'bg'=>'#052e16'],
    ['label'=>'Failed Today',   'value'=>$stats['sms_failed_today'], 'icon'=>'fa-circle-xmark',   'color'=>'#f87171', 'bg'=>'#3f0f0f'],
    ['label'=>'Total Sent',     'value'=>$stats['sms_total_sent'],   'icon'=>'fa-paper-plane',    'color'=>'#60a5fa', 'bg'=>'#0c1e40'],
];
@endphp

{{-- Stat Cards --}}
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:20px;">
    @foreach($cards as $card)
    <div class="card" style="display:flex;align-items:center;gap:14px;padding:16px;">
        <div style="width:42px;height:42px;border-radius:11px;background:{{ $card['bg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fa-solid {{ $card['icon'] }}" style="color:{{ $card['color'] }};font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:22px;font-weight:700;color:#f1f5f9;line-height:1.1;">{{ number_format($card['value']) }}</div>
            <div style="font-size:11px;color:#64748b;margin-top:2px;">{{ $card['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Chart + Recent Devices --}}
<div style="display:grid;grid-template-columns:1fr 320px;gap:16px;margin-bottom:16px;">
    <div class="card">
        <div style="font-size:13px;font-weight:600;color:#94a3b8;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-chart-bar" style="color:#2563eb;"></i> SMS Activity — Last 14 Days
        </div>
        <canvas id="smsChart" height="90"></canvas>
    </div>
    <div class="card">
        <div style="font-size:13px;font-weight:600;color:#94a3b8;margin-bottom:14px;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-mobile-screen" style="color:#a78bfa;"></i> Recent Devices
        </div>
        @forelse($recentDevices as $device)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid #111e2f;">
            <div>
                <div style="font-size:13px;font-weight:500;color:#e2e8f0;">{{ $device->name }}</div>
                <div style="font-size:11px;color:#475569;">{{ $device->client->name }}</div>
            </div>
            <span class="badge badge-{{ $device->status }}">{{ $device->status }}</span>
        </div>
        @empty
        <p style="font-size:13px;color:#475569;">No devices yet.</p>
        @endforelse
    </div>
</div>

{{-- Recent Messages + Errors --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div class="card">
        <div style="font-size:13px;font-weight:600;color:#94a3b8;margin-bottom:14px;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-comment-sms" style="color:#4ade80;"></i> Recent Messages
        </div>
        @forelse($recentMessages as $msg)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid #111e2f;">
            <div style="min-width:0;">
                <span style="font-size:13px;font-family:monospace;color:#f1f5f9;">{{ $msg->to_number }}</span>
                <span style="font-size:11px;color:#475569;margin-left:8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:inline-block;max-width:140px;vertical-align:middle;">{{ Str::limit($msg->message_body, 35) }}</span>
            </div>
            <span class="badge badge-{{ $msg->status }}" style="margin-left:8px;flex-shrink:0;">{{ $msg->status }}</span>
        </div>
        @empty
        <p style="font-size:13px;color:#475569;">No messages yet.</p>
        @endforelse
    </div>
    <div class="card">
        <div style="font-size:13px;font-weight:600;color:#94a3b8;margin-bottom:14px;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-circle-exclamation" style="color:#f87171;"></i> Recent Errors
        </div>
        @forelse($recentErrors as $log)
        <div style="padding:9px 0;border-bottom:1px solid #111e2f;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:2px;">
                <span style="font-size:11px;font-weight:600;color:#f87171;font-family:monospace;">{{ $log->type }}</span>
                <span style="font-size:11px;color:#475569;">{{ $log->created_at?->diffForHumans() }}</span>
            </div>
            <div style="font-size:12px;color:#94a3b8;">{{ Str::limit($log->message, 60) }}</div>
        </div>
        @empty
        <p style="font-size:13px;color:#475569;">No recent errors.</p>
        @endforelse
    </div>
</div>

<script>
const labels    = @json($smsByDay->pluck('date'));
const sentData  = @json($smsByDay->pluck('sent'));
const failedData= @json($smsByDay->pluck('failed'));
new Chart(document.getElementById('smsChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label:'Sent',   data:sentData,   backgroundColor:'#16a34a99', borderColor:'#22c55e', borderWidth:1, borderRadius:4 },
            { label:'Failed', data:failedData, backgroundColor:'#dc262699', borderColor:'#ef4444', borderWidth:1, borderRadius:4 },
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { labels: { color:'#64748b', font:{ size:12 } } }
        },
        scales: {
            x: { ticks:{ color:'#475569', font:{size:11} }, grid:{ color:'#111e2f' } },
            y: { ticks:{ color:'#475569', font:{size:11} }, grid:{ color:'#111e2f' } },
        }
    }
});
</script>
@endsection
