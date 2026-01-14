<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function created(User $user): void
    {
        Log::info("trigger");
        $this->syncUserRole($user);
    }

    public function updated(User $user): void
    {
        if (! $user->wasChanged('user_type')) {
            return;
        }
        $this->syncUserRole($user);
    }

    /**
     * SINGLE SOURCE OF TRUTH
     */
    protected function syncUserRole(User $user): void
    {
        $roleMap = [
            'admin'        => 'admin',
            'counsellor'   => 'counsellor',
            'program'      => 'program',
            'customer'     => 'customer',
            'super-admin'  => 'super-admin',
        ];

        if (! isset($roleMap[$user->user_type])) {
            Log::warning("User {$user->id} has unknown user_type: {$user->user_type}");
            return;
        }

        $user->syncRoles([$roleMap[$user->user_type]]);
    }
}
