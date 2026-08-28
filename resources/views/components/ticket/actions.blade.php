@if($ticket->status == App\Models\Ticket::STATUS_SOLVED)
    @if($ticket->getIssueId())
        <div class="float-right" style="margin-top:-20px; margin-right:60px;">
            @include('components.ticket.issue')
        </div>
    @endif
    <div class="float-right mt-4 mr4 ml-3">
        <form method="POST" action="{{ route('tickets.reopen', $ticket) }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary mt-1">Reabrir Ticket</button>
        </form>
    </div>
@else
    <div class="">
        @include('components.ticket.escalate')
        @include('components.ticket.idea')
        @include('components.ticket.issue')
    </div>
@endif