<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    // Constantes de estado para las vistas
    public const STATUS_NEW = 1;
    public const STATUS_OPEN = 2;
    public const STATUS_PENDING = 3;
    public const STATUS_SOLVED = 4;
    public const STATUS_CLOSED = 5;
    public const STATUS_MERGED = 6;

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

    /**
     * Lógica de Negocio: Verifica si el ticket ha sido escalado.
     */
    public function isEscalated()
    {
        // Temporalmente devolvemos falso para evitar errores. 
        // Más adelante puedes enlazar esto a una columna 'escalated' en tu BD si lo necesitas.
        return false;
    }

    /**
     * Lógica de Negocio: Obtiene el ID del problema en repositorios de código.
     */
    public function getIssueId()
    {
        // Retornamos null ya que descartamos la integración con Bitbucket para este Helpdesk interno.
        return null;
    }

    /**
     * Lógica de Negocio: Obtiene el nombre del estado en español.
     */
    public function statusName()
    {
        return match((int) $this->status) {
            self::STATUS_NEW => 'Nuevo',
            self::STATUS_OPEN => 'Abierto',
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_SOLVED => 'Resuelto',
            self::STATUS_CLOSED => 'Cerrado',
            self::STATUS_MERGED => 'Fusionado',
            default => 'Desconocido',
        };
    }

    /**
     * Lógica de Negocio: Obtiene la prioridad en español.
     */
    public function priorityName()
    {
        return match((int) $this->priority) {
            1 => 'Baja',
            2 => 'Normal',
            3 => 'Alta',
            default => 'Normal',
        };
    }

    /**
     * Lógica de Negocio: Obtiene los requerimientos que han sido agrupados o fusionados con este.
     */
    public function mergedTickets()
    {
        return $this->belongsToMany(Ticket::class, 'merged_tickets', 'ticket_id', 'merged_ticket_id');
    }

    /**
     * Lógica de Negocio: ¿Este ticket permite ediciones o nuevos comentarios?
     */
    public function canBeEdited()
    {
        // Un ticket cerrado ya no debe modificarse, a menos que sea reabierto por un agente.
        return $this->status != self::STATUS_CLOSED;
    }
}