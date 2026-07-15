<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->is_admin || $user->id === $booking->user_id;
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->is_admin || $user->id === $booking->user_id;
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->is_admin || $user->id === $booking->user_id;
    }
}