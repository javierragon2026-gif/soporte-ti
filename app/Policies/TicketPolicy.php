<?php

namespace App\Policies;

// Actualizamos las rutas a la estructura moderna de Laravel
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Lógica de Negocio: ¿Quién puede ver este ticket?
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ticket  $ticket
     * @return bool
     */
public function view(User $user, Ticket $ticket)
    {
        // Todo el personal autenticado de Sistemas tiene acceso de lectura
        return true;
    }

    /**
     * Lógica de Negocio: ¿Quién puede crear tickets nuevos?
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        // Cualquier agente de sistemas logueado puede generar un requerimiento
        return true;
    }

    /**
     * Lógica de Negocio: ¿Quién puede actualizar o responder un ticket?
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ticket  $ticket
     * @return bool
     */
    public function update(User $user, Ticket $ticket)
    {
        // Solo el agente asignado a este problema o un administrador general pueden modificarlo
        return $user->id == $ticket->user_id || $user->admin;
    }

    /**
     * Lógica de Negocio: ¿Quién puede eliminar un ticket del sistema?
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ticket  $ticket
     * @return bool
     */
    public function delete(User $user, Ticket $ticket)
    {
        // Por seguridad, solo los administradores tienen permiso de borrado
        return $user->admin;
    }
}