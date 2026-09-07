@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- Encabezado Limpio -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold" style="color: #67768A;"><i class="fas fa-ticket-alt me-2" style="color: #F4A637;"></i> Seguimiento de tu Reporte</h4>
                <a href="{{ route('cliente.tickets.index') }}" class="btn btn-outline-secondary btn-sm">Volver al Historial</a>
            </div>

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-body p-4 bg-light">
                    <div class="p-3 bg-white shadow-sm mb-4" style="border-radius: 8px; border-left: 4px solid #67768A;">
                        <h6 class="fw-bold mb-2">{{ $ticket->title }}</h6>
                        <p class="mb-0 text-muted">{{ $ticket->body }}</p>
                    </div>

                    <!-- Hilo de Comentarios (FILTRADO: Solo Notas Públicas) -->
                    @foreach($ticket->comments->where('private', 0) as $comment)
                        <div class="p-3 shadow-sm mb-3 bg-white" style="border-radius: 8px; border-left: 4px solid #61b0a5;">
                            <div class="d-flex justify-content-between mb-2">
                                <strong style="color: #61b0a5;">{{ $comment->user->name }} (Soporte TI)</strong>
                                <span class="text-muted small">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="mb-0 text-dark">{{ $comment->body }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Caja de Respuesta Cliente -->
            @if($ticket->status != 3)
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-top: 4px solid #61b0a5 !important;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 text-muted">¿Necesitas agregar más información?</h6>
                    <!-- Asegúrate de tener esta ruta configurada para que el usuario pueda comentar -->
                    <form method="POST" action="{{ route('requester.comments.store', $ticket) }}">
                        @csrf
                        <textarea name="body" class="form-control bg-light border-0 mb-3" rows="3" placeholder="Responde a Sistemas aquí..." required></textarea>
                        <div class="text-end">
                            <button type="submit" class="btn text-white fw-bold px-4" style="background-color: #61b0a5;">
                                Enviar Respuesta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection