<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    const Confirmed = 'confirmed';
    const Pending = 'pending';
    /**
     * Get the user (customer) who made the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the counsellor for this booking.
     */
    public function counsellor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the specific slot that was booked.
     */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }

    /**
     * Get the counselling session that resulted from this booking.
     */
    public function counsellingSession(): HasOne
    {
        return $this->hasOne(CounsellingSession::class);
    }
}
