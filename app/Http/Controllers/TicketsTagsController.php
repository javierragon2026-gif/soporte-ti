<?php

namespace App\Http\Controllers;

use use App\Models\Ticket;;

class TicketsTagsController extends Controller
{
    public function store(Ticket $ticket)
    {
        $ticket->attachTags([request('tag')]);

        return response()->json();
    }

    public function destroy(Ticket $ticket, $tag)
    {
        $ticket->detachTag($tag);

        return response()->json();
    }
}
