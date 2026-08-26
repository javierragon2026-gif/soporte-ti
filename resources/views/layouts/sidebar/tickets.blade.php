<h4>@icon(inbox) {{ trans_choice('ticket.ticket', 2) }}</h4>
<ul>
    @php
        $user = auth()->user();
        $totalAbiertos   = \App\Models\Ticket::where('status', 1)->count();
        $sinAsignar      = \App\Models\Ticket::whereNull('user_id')->count();
        $misTickets      = \App\Models\Ticket::where('user_id', $user->id ?? 0)->count();
    @endphp

    @if($user && isset($user->assistant) && $user->assistant)
        @include('components.sidebarItem', [
            "url" => route('tickets.index') . "?escalated=true",
            "title" => __('ticket.escalated'),
            "count" => \App\Models\Ticket::where('status', 2)->count()
        ])
    @endif

    @include('components.sidebarItem', [
        "url" => route('tickets.index') . "?all=true",
        "title" => __('ticket.open'),
        "count" => $totalAbiertos
    ])

    @include('components.sidebarItem', [
        "url" => route('tickets.index') . "?unassigned=true",
        "title" => __('ticket.unassigned'),
        "count" => $sinAsignar
    ])

    @include('components.sidebarItem', [
        "url" => route('tickets.index') . "?assigned=true",
        "title" => __('ticket.myTickets'),
        "count" => $misTickets
    ])

    @include('components.sidebarItem', [
        "url" => route('tickets.index') . "?recent=true",
        "title" => __('ticket.recent'),
        "count" => \App\Models\Ticket::latest('updated_at')->take(10)->count()
    ])

    @include('components.sidebarItem', [
        "url" => route('tickets.index') . "?solved=true",
        "title" => __('ticket.solved')
    ])

    @include('components.sidebarItem', [
        "url" => route('tickets.index') . "?closed=true",
        "title" => __('ticket.closed')
    ])
</ul>