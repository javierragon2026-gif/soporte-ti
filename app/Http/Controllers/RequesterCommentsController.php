<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class RequesterCommentsController extends Controller
{
    /**
     * Lógica de Negocio: Guarda un comentario del usuario final.
     */
    public function store($id, Request $request)
    {
        // Candados de seguridad para archivos adjuntos y contenido
        $request->validate([
            'body'          => 'required|string',
            'attachment'    => 'nullable|file|max:15360|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx,txt,zip,rar,7z,mp4,csv,xml',
            'attachments.*' => 'nullable|file|max:15360|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx,txt,zip,rar,7z,mp4,csv,xml',
        ], [
            'attachment.mimes'    => 'Tipo de archivo no permitido. Solo se aceptan imágenes, PDFs, Word, Excel, ZIP y Videos.',
            'attachment.max'      => 'El archivo no debe pesar más de 15MB.',
            'attachments.*.mimes' => 'Tipo de archivo no permitido. Solo se aceptan imágenes, PDFs, Word, Excel, ZIP y Videos.',
            'attachments.*.max'   => 'El archivo no debe pesar más de 15MB.',
        ]);

        // Buscamos el ticket directamente por su ID numérico
        $ticket = Ticket::findOrFail($id);
        
        // CORRECCIÓN: Pasamos el usuario autenticado en vez de "null"
        $comment = $ticket->addComment(auth()->user(), $request->input('body'), $this->getNewStatus());

        /*
         * Guardamos archivos adjuntos si existen (como lo hace TI).
         */
        if ($comment && request()->hasFile('attachments')) {
            foreach (request()->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $comment->attachments()->create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'user_id' => auth()->id(),
                ]);
            }
        } elseif ($comment && request()->hasFile('attachment')) {
            \App\Models\Attachment::storeAttachmentFromRequest(
                request(),
                $comment
            );
        }

        event(new \App\Events\TicketUpdated($ticket));
        return back();
    }

    /**
     * Determina el estado del ticket basándose en la solicitud.
     */
    private function getNewStatus()
    {
        if (request('solved')) {
            return Ticket::STATUS_SOLVED;
        }
        if (request('reopen')) {
            return Ticket::STATUS_OPEN;
        }
        
        return null;
    }
}
