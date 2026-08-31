<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Estados
    |--------------------------------------------------------------------------
    */

    public const STATUS_NEW = 1;
    public const STATUS_OPEN = 2;
    public const STATUS_PENDING = 3;
    public const STATUS_SOLVED = 4;
    public const STATUS_CLOSED = 5;
    public const STATUS_MERGED = 6;

    /*
    |--------------------------------------------------------------------------
    | Configuración
    |--------------------------------------------------------------------------
    */

    protected $table = 'tickets';

    protected $guarded = [];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Usuario asignado/creador del ticket.
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Sin asignar',
        ]);
    }

    /**
     * Equipo al que pertenece el ticket.
     */
    public function team()
    {
        return $this->belongsTo(Team::class)->withDefault([
            'name' => 'General',
        ]);
    }

    /**
     * Comentarios públicos del ticket.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'ticket_id');
    }

    /**
     * Notas privadas del ticket.
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'ticket_id');
    }

    /**
     * Tickets fusionados.
     */
    public function mergedTickets()
    {
        return $this->belongsToMany(
            Ticket::class,
            'merged_tickets',
            'ticket_id',
            'merged_ticket_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Comentarios
    |--------------------------------------------------------------------------
    */

    /**
     * Agrega un comentario público al ticket.
     *
     * @param User $user
     * @param string $body
     * @param mixed $newStatus
     * @return Comment
     */
    public function addComment($user, $body, $newStatus = null)
    {
        $comment = $this->comments()->create([
            'user_id' => $user->id,
            'body' => $body,
            'new_status' => $newStatus,
        ]);

        /*
         * Si se seleccionó un nuevo estado,
         * actualizamos el ticket.
         */
        if ($newStatus !== null && $newStatus !== '') {
            $this->update([
                'status' => $newStatus,
            ]);
        }

        return $comment;
    }

    /**
     * Agrega una nota privada al ticket.
     *
     * @param User $user
     * @param string $body
     * @return Note
     */
    public function addNote($user, $body)
    {
        return $this->notes()->create([
            'user_id' => $user->id,
            'body' => $body,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Lógica de negocio
    |--------------------------------------------------------------------------
    */

    /**
     * Verifica si el ticket ha sido escalado.
     */
    public function isEscalated()
    {
        return false;
    }

    /**
     * Obtiene el ID del problema en repositorios de código.
     */
    public function getIssueId()
    {
        return null;
    }

    /**
     * Obtiene el nombre del estado en español.
     */
    public function statusName()
    {
        return match ((int) $this->status) {
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
     * Obtiene la prioridad en español.
     */
    public function priorityName()
    {
        return match ((int) $this->priority) {
            1 => 'Baja',
            2 => 'Normal',
            3 => 'Alta',
            default => 'Normal',
        };
    }

    /**
     * Indica si el ticket puede modificarse.
     */
    public function canBeEdited()
    {
        return $this->status != self::STATUS_CLOSED;
    }
}
