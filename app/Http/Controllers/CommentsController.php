<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Ticket;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    /**
     * Guarda un comentario o nota en un ticket.
     */
    public function store(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        /*
         * Validamos los datos recibidos.
         */
        $request->validate([
            'body' => ['required', 'string'],
            'new_status' => ['nullable'],
            'private' => ['nullable', 'boolean'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        /*
         * Si es una nota privada.
         */
        if ($request->boolean('private')) {
            $comment = $ticket->addNote(
                auth()->user(),
                $request->input('body')
            );
        }

        /*
         * Si es un comentario público.
         */
        else {
            $comment = $ticket->addComment(
                auth()->user(),
                $request->input('body'),
                $request->input('new_status')
            );
        }

        /*
         * Guardamos el archivo adjunto si existe.
         */
        if ($comment && $request->hasFile('attachment')) {
            Attachment::storeAttachmentFromRequest(
                $request,
                $comment
            );
        }

        return redirect()->route('tickets.index');
    }
}
