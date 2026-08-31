<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';

    protected $guarded = [];

    /**
     * El comentario pertenece a un ticket.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * El comentario pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Archivos adjuntos del comentario.
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
