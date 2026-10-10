<x-mail::message>
# Notificación de solicitud

Hola **{{ $ticket->user->name ?? 'Usuario' }}**,

Su ticket ha cambiado al estado: **{{ $ticket->statusName() }}**.

@if(in_array($ticket->status, [4, 5]))
**Responsable de atención:** {{ $ticket->agent->name ?? 'Área de TI' }} — Departamento de Sistemas.

*Por favor, verificar que funcione correctamente. Si el inconveniente persiste, responda a este mensaje para dar seguimiento.*
@else
{{ $mensaje }}
@endif

**Detalles del Ticket:**
- **ID:** TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
- **Asunto:** {{ $ticket->title }}
- **Categoría:** {{ $ticket->categoria }}

<x-mail::button :url="route('cliente.tickets.show', $ticket)">
Ver Ticket Completo
</x-mail::button>

Gracias,<br>
**Equipo de Sistemas TI**<br>
{{ config('app.name') }}
</x-mail::message>
