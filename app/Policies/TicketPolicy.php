<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_id || $user->isAgent();
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $user->isAgent();
    }
}
