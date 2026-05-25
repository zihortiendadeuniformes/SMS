<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\BlockedNumber;
use App\Models\Client;
use App\Models\Device;
use App\Models\SmsLog;
use App\Models\SmsMessage;
use Illuminate\Support\Facades\DB;

class SmsService
{
    public function createMessage(array $data, Client $client, ?ApiKey $apiKey = null, ?Device $device = null): array
    {
        if (!$client->isActive()) {
            return ['success' => false, 'error' => 'Client account is inactive'];
        }

        if ($client->hasReachedDailyLimit()) {
            return ['success' => false, 'error' => 'Daily SMS limit reached'];
        }

        if ($client->hasReachedMonthlyLimit()) {
            return ['success' => false, 'error' => 'Monthly SMS limit reached'];
        }

        if ($apiKey) {
            if ($apiKey->hasReachedDailyLimit()) {
                return ['success' => false, 'error' => 'API key daily limit reached'];
            }
            if ($apiKey->hasReachedMonthlyLimit()) {
                return ['success' => false, 'error' => 'API key monthly limit reached'];
            }
        }

        $toNumber = $data['to'];

        if (BlockedNumber::isBlocked($toNumber, $client->id)) {
            return ['success' => false, 'error' => 'Destination number is blocked'];
        }

        if ($device) {
            if ($device->isDisabled()) {
                return ['success' => false, 'error' => 'Device is disabled'];
            }
            if (!$device->gateway_enabled) {
                return ['success' => false, 'error' => 'Device gateway is disabled'];
            }
        }

        $message = DB::transaction(function () use ($data, $client, $apiKey, $device) {
            $msg = SmsMessage::create([
                'client_id'    => $client->id,
                'device_id'    => $device?->id,
                'api_key_id'   => $apiKey?->id,
                'to_number'    => $data['to'],
                'message_body' => $data['message'],
                'priority'     => $data['priority'] ?? 5,
                'max_attempts' => $data['max_attempts'] ?? 3,
                'status'       => 'pending',
            ]);

            $client->increment('used_sms_today');
            $client->increment('used_sms_month');

            if ($apiKey) {
                $apiKey->increment('used_today');
                $apiKey->increment('used_month');
                $apiKey->update(['last_used_at' => now()]);
            }

            SmsLog::create([
                'client_id'      => $client->id,
                'device_id'      => $device?->id,
                'sms_message_id' => $msg->id,
                'type'           => 'sms_created',
                'level'          => 'info',
                'message'        => "SMS created to {$data['to']}",
                'context'        => ['message_id' => $msg->id, 'to' => $data['to']],
            ]);

            return $msg;
        });

        return ['success' => true, 'message' => $message];
    }

    public function reserveMessage(SmsMessage $message, Device $device): bool
    {
        if ($message->status !== 'pending') {
            return false;
        }

        $updated = SmsMessage::where('id', $message->id)
            ->where('status', 'pending')
            ->update([
                'status'      => 'reserved',
                'device_id'   => $device->id,
                'reserved_at' => now(),
            ]);

        if ($updated) {
            SmsLog::create([
                'client_id'      => $message->client_id,
                'device_id'      => $device->id,
                'sms_message_id' => $message->id,
                'type'           => 'sms_reserved',
                'level'          => 'info',
                'message'        => "SMS #{$message->id} reserved by device {$device->name}",
            ]);
        }

        return (bool) $updated;
    }

    public function markSent(SmsMessage $message, ?array $providerResponse = null): void
    {
        $message->update([
            'status'            => 'sent',
            'sent_at'           => now(),
            'provider_response' => $providerResponse,
        ]);

        SmsLog::create([
            'client_id'      => $message->client_id,
            'device_id'      => $message->device_id,
            'sms_message_id' => $message->id,
            'type'           => 'sms_sent',
            'level'          => 'info',
            'message'        => "SMS #{$message->id} sent successfully to {$message->to_number}",
        ]);
    }

    public function autoDispatch(SmsMessage $message, Device $device, Client $client): void
    {
        // Reserve it so APK doesn't double-process
        $reserved = SmsMessage::where('id', $message->id)
            ->where('status', 'pending')
            ->update([
                'status'      => 'reserved',
                'device_id'   => $device->id,
                'reserved_at' => now(),
            ]);

        if (!$reserved) return;

        // Mark as sent immediately (APK will send via SIM, backend tracks it as sent)
        SmsMessage::where('id', $message->id)->update([
            'status'  => 'sent',
            'sent_at' => now(),
        ]);

        SmsLog::create([
            'client_id'      => $client->id,
            'device_id'      => $device->id,
            'sms_message_id' => $message->id,
            'type'           => 'sms_sent',
            'level'          => 'info',
            'message'        => "SMS #{$message->id} dispatched to {$message->to_number} via {$device->name}",
        ]);
    }

    public function markFailed(SmsMessage $message, string $error, ?array $providerResponse = null): void
    {
        $attempts = $message->attempts + 1;
        $canRetry = $attempts < $message->max_attempts;

        $message->update([
            'status'            => $canRetry ? 'pending' : 'failed',
            'attempts'          => $attempts,
            'error_message'     => $error,
            'provider_response' => $providerResponse,
            'failed_at'         => $canRetry ? null : now(),
            'reserved_at'       => null,
        ]);

        SmsLog::create([
            'client_id'      => $message->client_id,
            'device_id'      => $message->device_id,
            'sms_message_id' => $message->id,
            'type'           => 'sms_failed',
            'level'          => 'error',
            'message'        => "SMS #{$message->id} failed: {$error}",
            'context'        => ['attempts' => $attempts, 'can_retry' => $canRetry],
        ]);
    }
}
