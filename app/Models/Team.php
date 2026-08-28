<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     * Nota: Si en el futuro decides renombrar la tabla a 'equipos_ti', 
     * solo debes modificar esta línea.
     */
    protected $table = 'teams';

    /**
     * Atributos asignables en masa. 
     * Se deja vacío para permitir inserciones rápidas durante el desarrollo.
     */
    protected $guarded = [];

    /**
     * Lógica de Negocio: Un equipo de soporte atiende múltiples requerimientos.
     */
    public function requerimientos()
    {
        // Renombramos la función de 'tickets()' a 'requerimientos()' para hacerlo tuyo
        return $this->hasMany(Ticket::class);
    }

    /**
     * Lógica de Negocio: Personal (agentes) asignado a este equipo de trabajo.
     * Conecta con la tabla pivote de membresías.
     */
    public function miembros()
    {
        // Renombramos de 'members()' a 'miembros()'
        return $this->belongsToMany(User::class, 'memberships', 'team_id', 'user_id');
    }
}