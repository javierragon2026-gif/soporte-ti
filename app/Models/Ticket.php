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

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Sin asignar',
        ]);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id')->withDefault([
            'name' => 'Sin asignar',
        ]);
    }

    public function team()
    {
        return $this->belongsTo(Team::class)->withDefault([
            'name' => 'General',
        ]);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'ticket_id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'ticket_id');
    }

    public function mergedTickets()
    {
        return $this->belongsToMany(
            Ticket::class,
            'merged_tickets',
            'ticket_id',
            'merged_ticket_id'
        );
    }

    /**
     * Relación de archivos adjuntos agregada para evitar el colapso (Error 500).
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /*
    |--------------------------------------------------------------------------
    | Comentarios
    |--------------------------------------------------------------------------
    */

    public function addComment($user, $body, $newStatus = null)
    {
        $comment = $this->comments()->create([
            'user_id' => $user->id,
            'body' => $body,
            'new_status' => $newStatus,
        ]);

        if ($newStatus !== null && $newStatus !== '') {
            $this->update([
                'status' => $newStatus,
            ]);
        }

        return $comment;
    }

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

    public function isEscalated()
    {
        return false;
    }

    public function getIssueId()
    {
        return null;
    }

    public function statusName()
    {
        return match ((int) $this->status) {
            self::STATUS_NEW => 'Abierto',
            self::STATUS_OPEN => 'En Proceso',
            self::STATUS_PENDING => 'Pendiente de Información',
            self::STATUS_SOLVED => 'Resuelto',
            self::STATUS_CLOSED => 'Cerrado',
            self::STATUS_MERGED => 'Fusionado',
            default => 'Desconocido',
        };
    }

    public function statusBadgeBg()
    {
        return match ((int) $this->status) {
            self::STATUS_NEW => '#e0f2fe',
            self::STATUS_OPEN => '#dbeafe',
            self::STATUS_PENDING => '#fef3c7',
            self::STATUS_SOLVED => '#d1fae5',
            self::STATUS_CLOSED => '#f1f5f9',
            default => '#f1f5f9',
        };
    }

    public function statusBadgeColor()
    {
        return match ((int) $this->status) {
            self::STATUS_NEW => '#0284c7',
            self::STATUS_OPEN => '#1d4ed8',
            self::STATUS_PENDING => '#d97706',
            self::STATUS_SOLVED => '#047857',
            self::STATUS_CLOSED => '#475569',
            default => '#475569',
        };
    }

    public function priorityName()
    {
        return match ((int) $this->priority) {
            1 => 'Baja',
            2 => 'Normal',
            3 => 'Alta',
            default => 'Normal',
        };
    }

    public function canBeEdited()
    {
        return $this->status != self::STATUS_CLOSED;
    }

}