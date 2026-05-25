<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $client = $request->attributes->get('client');

        return response()->json([
            'success' => true,
            'balance' => [
                'daily_limit'     => $client->daily_sms_limit,
                'monthly_limit'   => $client->monthly_sms_limit,
                'used_today'      => $client->used_sms_today,
                'used_month'      => $client->used_sms_month,
                'remaining_today' => max(0, $client->daily_sms_limit - $client->used_sms_today),
                'remaining_month' => max(0, $client->monthly_sms_limit - $client->used_sms_month),
            ],
        ]);
    }
}
