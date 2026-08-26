<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'tickets';

    /**
     * Atributos asignables en masa.
     */
    protected $guarded = [];

    /**
     * Relación: Un ticket pertenece al usuario asignado/creador.
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Sin asignar',
        ]);
    }

    /**
     * Relación: Un ticket puede pertenecer a un equipo.
     */
    public function team()
    {
        return $this->belongsTo(Team::class)->withDefault([
            'name' => 'General',
        ]);
    }
}