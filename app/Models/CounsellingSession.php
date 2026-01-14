<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CounsellingSession extends Model
{
    /**
     * Get the booking that this session belongs to.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
    
    /**
     * Get the counsellor for this session.
     */
    public function counsellor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    /**
     * Get the customer for this session.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    
    /**
     * Get the program associated with this session.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(programDetail::class, 'program_id');
    }
}
