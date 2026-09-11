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
            'attachments.*' => ['nullable', 'file', 'max:10240'],
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
         * Guardamos archivos adjuntos si existen.
         */
        if ($comment && $request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $comment->attachments()->create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'user_id' => auth()->id(),
                ]);
            }
        } elseif ($comment && $request->hasFile('attachment')) {
            Attachment::storeAttachmentFromRequest(
                $request,
                $comment
            );
        }

        return redirect()->route('tickets.show', $ticket);
    }
}
