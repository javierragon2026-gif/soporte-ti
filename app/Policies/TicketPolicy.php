<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Ticket $ticket)
    {
        return $user->is_it_admin || $user->id === $ticket->user_id;
    }

    public function update(User $user, Ticket $ticket)
    {
        return $user->is_it_admin;
    }

    public function delete(User $user, Ticket $ticket)
    {
        return in_array(strtolower($user->username), ['jramirez', 'rguzman']);
    }
}
