@extends('layouts.app')

@section('title', 'Integrations')

@section('content')
<div style="max-width:960px;">

    {{-- Header --}}
    <div style="margin-bottom:28px;">
        <h1 style="font-size:22px;font-weight:700;color:#f1f5f9;margin:0 0 6px;">
            <i class="fa-solid fa-plug-circle-bolt" style="color:#2563eb;margin-right:8px;"></i>Integrations
        </h1>
        <p style="color:#475569;font-size:13px;margin:0;">
            Connect any external system to SendBridge to send SMS via API.
        </p>
    </div>

    {{-- Quick start --}}
    <div class="card" style="margin-bottom:20px;padding:22px 24px;">
        <div style="font-size:14px;font-weight:700;color:#f1f5f9;margin-bottom:14px;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-bolt" style="color:#f59e0b;"></i> Quick Start
        </div>
        <p style="color:#64748b;font-size:13px;margin:0 0 14px;">
            Your base API URL — add this to your external system's HTTP client:
        </p>
        <div style="display:flex;align-items:center;gap:10px;">
            <code id="baseUrlCode" style="flex:1;background:#0a1525;border:1px solid #1e2d45;border-radius:8px;padding:10px 14px;font-size:13px;color:#4ade80;font-family:monospace;">{{ $baseUrl }}/api/v1</code>
            <button onclick="copyText('baseUrlCode')" style="background:#1e2d45;border:none;border-radius:8px;padding:9px 14px;color:#94a3b8;cursor:pointer;font-size:12px;">
                <i class="fa-solid fa-copy"></i> Copy
            </button>
        </div>
    </div>

    {{-- API Keys --}}
    <div class="card" style="margin-bottom:20px;padding:22px 24px;">
        <div style="font-size:14px;font-weight:700;color:#f1f5f9;margin-bottom:14px;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-key" style="color:#a78bfa;"></i> Your API Keys
        </div>
        @if($apiKeys->isEmpty())
            <p style="color:#475569;font-size:13px;">No active API keys.
                <a href="{{ route('admin.api_keys.create') }}" style="color:#2563eb;">Create one →</a>
            </p>
        @else
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="color:#475569;border-bottom:1px solid #1e2d45;">
                    <th style="text-align:left;padding:6px 10px;">Client</th>
                    <th style="text-align:left;padding:6px 10px;">Key</th>
                    <th style="text-align:left;padding:6px 10px;">Daily Limit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($apiKeys as $key)
                <tr style="border-bottom:1px solid #0d1a2e;">
                    <td style="padding:8px 10px;color:#cbd5e1;">{{ $key->client->name ?? '—' }}</td>
                    <td style="padding:8px 10px;">
                        <code id="key-{{ $key->id }}" style="background:#0a1525;padding:4px 8px;border-radius:5px;color:#4ade80;font-family:monospace;font-size:12px;">{{ $key->key }}</code>
                        <button onclick="copyText('key-{{ $key->id }}')" style="background:none;border:none;color:#475569;cursor:pointer;margin-left:4px;"><i class="fa-solid fa-copy fa-sm"></i></button>
                    </td>
                    <td style="padding:8px 10px;color:#64748b;">{{ $key->daily_limit ? number_format($key->daily_limit) : '∞' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- Send SMS Endpoint --}}
    <div class="card" style="margin-bottom:20px;padding:22px 24px;">
        <div style="font-size:14px;font-weight:700;color:#f1f5f9;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-paper-plane" style="color:#2563eb;"></i> Send SMS — <code style="font-size:12px;color:#94a3b8;">POST /api/v1/sms/send</code>
        </div>

        {{-- Tabs --}}
        <div style="display:flex;gap:6px;margin-bottom:16px;" id="tabs">
            <button onclick="showTab('curl')"    class="tab-btn active" id="tab-curl">cURL</button>
            <button onclick="showTab('laravel')" class="tab-btn"        id="tab-laravel">Laravel / PHP</button>
            <button onclick="showTab('js')"      class="tab-btn"        id="tab-js">JavaScript</button>
            <button onclick="showTab('python')"  class="tab-btn"        id="tab-python">Python</button>
        </div>

        <div id="code-curl">
<pre style="background:#060d1a;border:1px solid #1e2d45;border-radius:9px;padding:16px;font-size:12px;color:#e2e8f0;overflow-x:auto;margin:0;"><code>curl -X POST {{ $baseUrl }}/api/v1/sms/send \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "to": "+1234567890",
    "message": "Hello from SendBridge!",
    "device_id": null
  }'</code></pre>
        </div>

        <div id="code-laravel" style="display:none;">
<pre style="background:#060d1a;border:1px solid #1e2d45;border-radius:9px;padding:16px;font-size:12px;color:#e2e8f0;overflow-x:auto;margin:0;"><code>use Illuminate\Support\Facades\Http;

// In your Laravel controller or service:
$response = Http::withToken('YOUR_API_KEY')
    ->post('{{ $baseUrl }}/api/v1/sms/send', [
        'to'      => '+1234567890',
        'message' => 'Hello from SendBridge!',
    ]);

if ($response->successful()) {
    $messageId = $response->json('message_id');
    // Save $messageId to track status later
} else {
    $error = $response->json('error');
}</code></pre>
        </div>

        <div id="code-js" style="display:none;">
<pre style="background:#060d1a;border:1px solid #1e2d45;border-radius:9px;padding:16px;font-size:12px;color:#e2e8f0;overflow-x:auto;margin:0;"><code>const response = await fetch('{{ $baseUrl }}/api/v1/sms/send', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer YOUR_API_KEY',
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    to: '+1234567890',
    message: 'Hello from SendBridge!',
  }),
});

const data = await response.json();
console.log('Message ID:', data.message_id);</code></pre>
        </div>

        <div id="code-python" style="display:none;">
<pre style="background:#060d1a;border:1px solid #1e2d45;border-radius:9px;padding:16px;font-size:12px;color:#e2e8f0;overflow-x:auto;margin:0;"><code>import requests

response = requests.post(
    '{{ $baseUrl }}/api/v1/sms/send',
    headers={'Authorization': 'Bearer YOUR_API_KEY'},
    json={
        'to': '+1234567890',
        'message': 'Hello from SendBridge!',
    }
)

data = response.json()
print('Message ID:', data.get('message_id'))</code></pre>
        </div>

        {{-- Response --}}
        <div style="margin-top:16px;">
            <div style="font-size:12px;color:#475569;margin-bottom:8px;font-weight:600;">RESPONSE <span style="color:#4ade80;">201 Created</span></div>
<pre style="background:#060d1a;border:1px solid #1e2d45;border-radius:9px;padding:14px;font-size:12px;color:#e2e8f0;overflow-x:auto;margin:0;"><code>{
  "success": true,
  "message_id": "msg_01j2x...",
  "status": "queued"
}</code></pre>
        </div>
    </div>

    {{-- Check status --}}
    <div class="card" style="margin-bottom:20px;padding:22px 24px;">
        <div style="font-size:14px;font-weight:700;color:#f1f5f9;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-magnifying-glass" style="color:#0891b2;"></i> Check Message Status — <code style="font-size:12px;color:#94a3b8;">GET /api/v1/sms/{id}</code>
        </div>
<pre style="background:#060d1a;border:1px solid #1e2d45;border-radius:9px;padding:14px;font-size:12px;color:#e2e8f0;overflow-x:auto;margin:0;"><code>// Laravel example
$status = Http::withToken('YOUR_API_KEY')
    ->get('{{ $baseUrl }}/api/v1/sms/msg_01j2x...')
    ->json();

// $status['status'] => 'queued' | 'reserved' | 'sent' | 'failed'</code></pre>
    </div>

    {{-- Webhook --}}
    <div class="card" style="margin-bottom:20px;padding:22px 24px;">
        <div style="font-size:14px;font-weight:700;color:#f1f5f9;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-webhook" style="color:#f59e0b;"></i> Webhooks (Status Callbacks)
        </div>
        <p style="color:#64748b;font-size:13px;margin:0 0 14px;">
            SendBridge can POST to your system when a message status changes.
            Configure your webhook URL in <a href="{{ route('admin.settings.index') }}" style="color:#2563eb;">Settings</a>.
        </p>
        <div style="font-size:12px;color:#475569;margin-bottom:8px;font-weight:600;">PAYLOAD SENT TO YOUR URL</div>
<pre style="background:#060d1a;border:1px solid #1e2d45;border-radius:9px;padding:14px;font-size:12px;color:#e2e8f0;overflow-x:auto;margin:0 0 16px;"><code>{
  "event":      "sms.sent",
  "message_id": "msg_01j2x...",
  "to":         "+1234567890",
  "status":     "sent",
  "sent_at":    "2026-05-23T06:00:00.000Z"
}</code></pre>

        {{-- Test webhook --}}
        <div style="font-size:13px;font-weight:600;color:#94a3b8;margin-bottom:10px;">Test your webhook endpoint:</div>
        <div style="display:flex;gap:10px;align-items:center;">
            <input type="url" id="webhookUrl" placeholder="https://your-system.com/webhook/sms"
                style="flex:1;background:#0a1525;border:1px solid #1e2d45;border-radius:8px;padding:10px 12px;font-size:13px;color:#f1f5f9;outline:none;">
            <button onclick="testWebhook()" id="btnTestWebhook"
                style="background:#1e2d45;border:1px solid #2563eb33;border-radius:8px;padding:10px 16px;color:#2563eb;cursor:pointer;font-size:13px;white-space:nowrap;">
                <i class="fa-solid fa-paper-plane"></i> Send Test
            </button>
        </div>
        <div id="webhookResult" style="margin-top:10px;display:none;"></div>
    </div>

    {{-- Request params table --}}
    <div class="card" style="padding:22px 24px;">
        <div style="font-size:14px;font-weight:700;color:#f1f5f9;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-table-list" style="color:#475569;"></i> API Reference
        </div>
        <table style="width:100%;border-collapse:collapse;font-size:12px;">
            <thead>
                <tr style="color:#475569;border-bottom:1px solid #1e2d45;">
                    <th style="text-align:left;padding:7px 10px;">Parameter</th>
                    <th style="text-align:left;padding:7px 10px;">Type</th>
                    <th style="text-align:left;padding:7px 10px;">Required</th>
                    <th style="text-align:left;padding:7px 10px;">Description</th>
                </tr>
            </thead>
            <tbody style="color:#94a3b8;">
                <tr style="border-bottom:1px solid #0d1a2e;">
                    <td style="padding:8px 10px;"><code style="color:#4ade80;">to</code></td>
                    <td style="padding:8px 10px;">string</td>
                    <td style="padding:8px 10px;"><span style="color:#f87171;">required</span></td>
                    <td style="padding:8px 10px;">Destination phone number in E.164 format (+1234567890)</td>
                </tr>
                <tr style="border-bottom:1px solid #0d1a2e;">
                    <td style="padding:8px 10px;"><code style="color:#4ade80;">message</code></td>
                    <td style="padding:8px 10px;">string</td>
                    <td style="padding:8px 10px;"><span style="color:#f87171;">required</span></td>
                    <td style="padding:8px 10px;">SMS body text (max 1600 chars)</td>
                </tr>
                <tr style="border-bottom:1px solid #0d1a2e;">
                    <td style="padding:8px 10px;"><code style="color:#4ade80;">device_id</code></td>
                    <td style="padding:8px 10px;">integer</td>
                    <td style="padding:8px 10px;color:#64748b;">optional</td>
                    <td style="padding:8px 10px;">Force a specific device. Null = auto-select best available</td>
                </tr>
                <tr style="border-bottom:1px solid #0d1a2e;">
                    <td style="padding:8px 10px;"><code style="color:#4ade80;">scheduled_at</code></td>
                    <td style="padding:8px 10px;">datetime</td>
                    <td style="padding:8px 10px;color:#64748b;">optional</td>
                    <td style="padding:8px 10px;">ISO 8601 datetime to schedule message delivery</td>
                </tr>
                <tr>
                    <td style="padding:8px 10px;"><code style="color:#4ade80;">external_id</code></td>
                    <td style="padding:8px 10px;">string</td>
                    <td style="padding:8px 10px;color:#64748b;">optional</td>
                    <td style="padding:8px 10px;">Your own reference ID for tracking</td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<style>
.tab-btn {
    background: #0a1525;
    border: 1px solid #1e2d45;
    border-radius: 7px;
    padding: 6px 14px;
    font-size: 12px;
    color: #64748b;
    cursor: pointer;
    transition: all .15s;
}
.tab-btn.active, .tab-btn:hover {
    background: #1e2d45;
    color: #f1f5f9;
    border-color: #2563eb44;
}
</style>

<script>
function showTab(tab) {
    ['curl','laravel','js','python'].forEach(t => {
        document.getElementById('code-' + t).style.display = t === tab ? 'block' : 'none';
        document.getElementById('tab-' + t).classList.toggle('active', t === tab);
    });
}

function copyText(elementId) {
    const text = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(text).then(() => {
        const el = document.getElementById(elementId);
        const orig = el.style.color;
        el.style.color = '#4ade80';
        setTimeout(() => el.style.color = orig, 1000);
    });
}

function testWebhook() {
    const url = document.getElementById('webhookUrl').value;
    const btn = document.getElementById('btnTestWebhook');
    const result = document.getElementById('webhookResult');
    if (!url) return;

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';

    fetch('{{ route('admin.integrations.webhook.test') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ url })
    })
    .then(r => r.json())
    .then(data => {
        result.style.display = 'block';
        if (data.success) {
            result.innerHTML = '<div style="background:#0a2218;border:1px solid #16a34a44;border-radius:8px;padding:10px 14px;color:#4ade80;font-size:12px;"><i class="fa-solid fa-circle-check"></i> Webhook responded with HTTP ' + data.status + '</div>';
        } else {
            result.innerHTML = '<div style="background:#1f0707;border:1px solid #dc262644;border-radius:8px;padding:10px 14px;color:#f87171;font-size:12px;"><i class="fa-solid fa-circle-xmark"></i> Error: ' + (data.error || 'HTTP ' + data.status) + '</div>';
        }
    })
    .catch(e => {
        result.style.display = 'block';
        result.innerHTML = '<div style="background:#1f0707;border:1px solid #dc262644;border-radius:8px;padding:10px 14px;color:#f87171;font-size:12px;">Network error: ' + e.message + '</div>';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send Test';
    });
}
</script>
@endsection
