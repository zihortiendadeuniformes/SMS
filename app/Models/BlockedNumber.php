<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedNumber extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'phone_number', 'reason'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public static function isBlocked(string $phoneNumber, ?int $clientId = null): bool
    {
        return static::where('phone_number', $phoneNumber)
            ->where(function ($q) use ($clientId) {
                $q->whereNull('client_id');
                if ($clientId) {
                    $q->orWhere('client_id', $clientId);
                }
            })->exists();
    }
}
