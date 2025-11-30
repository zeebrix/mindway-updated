<?php

namespace App\Policies;

use App\Models\Session; // Assuming you have a Session model
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SessionPolicy
{
    /**
     * Grant all abilities to admins before other checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'admin') {
            return true;
        }
        return null; // Let the other policy methods decide
    }

    /**
     * Determine whether the user can view a list of sessions.
     * - Counsellors can view their own sessions.
     * - Program managers can view sessions for their employees.
     */
    public function viewAny(User $user): bool
    {
        // Admins are handled by before().
        // Counsellors and Program managers can view lists of sessions relevant to them.
        return in_array($user->role, ['counsellor', 'program']);
    }

    /**
     * Determine whether the user can view a specific session.
     * - A user can view a session if they are the counsellor for it.
     * - A user can view a session if they are the customer who booked it.
     * - A program manager can view a session if it belongs to one of their employees.
     */
    public function view(User $user, Session $session): bool
    {
        // Is the user the counsellor assigned to this session?
        if ($user->role === 'counsellor' && $user->id === $session->counsellor_id) {
            return true;
        }

        // Is the user the customer who booked this session?
        if ($user->role === 'customer' && $user->id === $session->customer_id) {
            return true;
        }
        
        // Is the user a program manager for the customer in this session?
        // This requires a relationship: $user->employees->contains($session->customer_id)
        if ($user->role === 'program' && $user->managesEmployee($session->customer_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create sessions.
     * - Admins can create sessions.
     * - Customers can request/create sessions for themselves.
     */
    public function create(User $user): bool
    {
        // Admins are handled by before().
        // Customers are the ones who initiate booking a session.
        return $user->role === 'customer';
    }

    /**
     * Determine whether the user can update (e.g., rebook/cancel) the session.
     * - A counsellor can update a session they are assigned to.
     * - A customer can update a session they booked.
     */
    public function update(User $user, Session $session): bool
    {
        // This reuses the logic from the `view` method, as the permissions are the same.
        return $this->view($user, $session);
    }

    /**
     * Determine whether the user can delete the session.
     * Generally, it's better to "cancel" a session (update its status) than to delete it.
     * We will restrict deletion to admins only via the before() method.
     */
    public function delete(User $user, Session $session): bool
    {
        // Only admins (handled by before()) can delete.
        return false;
    }
}
