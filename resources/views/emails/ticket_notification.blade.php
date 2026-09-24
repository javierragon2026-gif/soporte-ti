<x-mail::message>
# {{ $evento }}

Hola **{{ $ticket->user->name ?? 'Usuario' }}**,

{{ $mensaje }}

**Detalles del Ticket:**
- **ID:** TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
- **Asunto:** {{ $ticket->title }}
- **Estado Actual:** {{ $ticket->statusName() }}
- **Categoría:** {{ $ticket->categoria }}

<x-mail::button :url="route('cliente.tickets.show', $ticket)">
Ver Ticket Completo
</x-mail::button>

Gracias,<br>
**Equipo de Sistemas TI**<br>
{{ config('app.name') }}
</x-mail::message>
