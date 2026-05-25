<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class IntegrationController extends Controller
{
    public function index(): View
    {
        $apiKeys = ApiKey::with('client')->where('is_active', true)->get();
        $baseUrl = config('app.url');

        return view('admin.integrations.index', compact('apiKeys', 'baseUrl'));
    }

    public function testWebhook(Request $request): JsonResponse
    {
        $request->validate(['url' => 'required|url']);

        $payload = [
            'event'     => 'sms.sent',
            'message_id' => 'test-' . uniqid(),
            'to'        => '+1234567890',
            'status'    => 'sent',
            'sent_at'   => now()->toISOString(),
        ];

        try {
            $response = Http::timeout(10)->post($request->url, $payload);
            return response()->json([
                'success' => $response->successful(),
                'status'  => $response->status(),
                'body'    => $response->body(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }
}
