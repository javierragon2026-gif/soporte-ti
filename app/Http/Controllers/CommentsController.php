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
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,csv,txt', 'max:15000'],
            'attachments.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,csv,txt', 'max:15000'],
        ], [
            'attachments.*.mimes' => 'El archivo adjunto debe ser de un formato válido (Imagen, PDF, Word, Excel).',
            'attachments.*.max' => 'El archivo adjunto no puede pesar más de 15MB.',
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
            
            // 📧 Enviar notificación por correo al creador del ticket si fue TI quien comentó
            if (auth()->user()->id !== $ticket->user_id) {
                $mensajeCorreo = "El agente **" . auth()->user()->name . "** ha agregado un nuevo comentario a tu ticket:\n\n> " . strip_tags($request->input('body'));
                try {
                    \Illuminate\Support\Facades\Mail::to($ticket->user->email)->send(new \App\Mail\TicketNotification($ticket, 'Nuevo Comentario de Sistemas', $mensajeCorreo));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Error enviando correo de comentario: ' . $e->getMessage());
                }
            }
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
