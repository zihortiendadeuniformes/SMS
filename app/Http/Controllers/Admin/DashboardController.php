<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Device;
use App\Models\SmsLog;
use App\Models\SmsMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'total_clients'       => Client::count(),
            'total_devices'       => Device::count(),
            'devices_online'      => Device::where('status', 'online')->count(),
            'devices_offline'     => Device::where('status', 'offline')->count(),
            'devices_disabled'    => Device::where('status', 'disabled')->count(),
            'sms_pending'         => SmsMessage::where('status', 'pending')->count(),
            'sms_reserved'        => SmsMessage::where('status', 'reserved')->count(),
            'sms_sent_today'      => SmsMessage::where('status', 'sent')->whereDate('sent_at', today())->count(),
            'sms_failed_today'    => SmsMessage::where('status', 'failed')->whereDate('failed_at', today())->count(),
            'sms_total_sent'      => SmsMessage::where('status', 'sent')->count(),
        ];

        $recentErrors = SmsLog::where('level', 'error')
            ->with(['device', 'client'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentMessages = SmsMessage::with(['client', 'device'])
            ->whereIn('status', ['sent', 'failed'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $recentDevices = Device::with('client')
            ->orderBy('last_seen_at', 'desc')
            ->limit(5)
            ->get();

        $smsByDay = SmsMessage::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent'),
                DB::raw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed')
            )
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'recentErrors', 'recentMessages', 'recentDevices', 'smsByDay'
        ));
    }
}
