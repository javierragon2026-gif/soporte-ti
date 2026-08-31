<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $table = 'notes';

    protected $guarded = [];

    /**
     * La nota pertenece a un ticket.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * La nota pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Archivos adjuntos de la nota.
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
