<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;

    /**
     * Create a new event instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            // Podemos tener un canal global para el dashboard de TI
            new PrivateChannel('tickets'),
        ];
    }
    
    /**
     * Datos a enviar en el socket.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->ticket->id,
            'status' => $this->ticket->status,
            'title' => $this->ticket->title,
            'user' => $this->ticket->user->name ?? 'Usuario',
            'agent_id' => $this->ticket->agent_id,
        ];
    }
}
