<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    /**
     * Enlace directo con la tabla original de Handesk.
     * Si después decides renombrarla a 'equipos_ti', solo cambias este valor.
     */
    protected $table = 'teams';

    /**
     * Desactivamos las protecciones de asignación masiva 
     * para facilitar la inserción de datos en desarrollo.
     */
    protected $guarded = [];

    /**
     * Lógica de Negocio: Un área o equipo de soporte atiende múltiples casos.
     */
    public function requerimientos()
    {
        // Vinculamos usando tu modelo de Ticket moderno
        return $this->hasMany(Ticket::class, 'team_id');
    }

    /**
     * Lógica de Negocio: Agentes de sistemas que pertenecen a este equipo.
     * Utiliza la tabla pivote 'memberships' de la base original.
     */
    public function agentes()
    {
        return $this->belongsToMany(User::class, 'memberships', 'team_id', 'user_id');
    }
}