@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Panel Izquierdo: Chat y Seguimiento -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-bold mb-0" style="color: #2c3e50;">{{ $ticket->title }}</h5>
                        <span class="badge bg-primary">Ticket #{{ $ticket->id }}</span>
                    </div>
                    <p class="text-muted mb-0 small">
                        <i class="fas fa-user me-1"></i> Solicitado por: <strong>{{ $ticket->user->name ?? 'Usuario' }}</strong>
                    </p>
                </div>
                <div class="card-body p-4 bg-light">
                    <!-- Problema Original -->
                    <div class="p-3 bg-white shadow-sm mb-4" style="border-radius: 8px; border-left: 4px solid #67768A;">
                        <p class="mb-0 text-dark">{{ $ticket->body }}</p>
                    </div>

                    <!-- Hilo de Comentarios -->
                    <h6 class="fw-bold text-muted mb-3 text-uppercase small">Historial de Seguimiento</h6>
                    @foreach($ticket->comments as $comment)
                        <div class="p-3 shadow-sm mb-3" style="border-radius: 8px; {{ $comment->private ? 'background-color: #FFF8F0; border-left: 4px solid #F4A637;' : 'background-color: #ffffff; border-left: 4px solid #61b0a5;' }}">
                            <div class="d-flex justify-content-between mb-2">
                                <strong style="color: #2c3e50;">{{ $comment->user->name }}</strong>
                                <span class="text-muted small">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="mb-0">{{ $comment->body }}</p>
                            @if($comment->private)
                                <span class="badge bg-warning text-dark mt-2" style="font-size: 0.7rem;"><i class="fas fa-eye-slash me-1"></i>Nota Privada TI</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Caja de Respuesta TI -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-top: 4px solid #F4A637 !important;">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('comments.store', $ticket) }}">
                        @csrf
                        <textarea name="body" class="form-control bg-light border-0 mb-3" rows="4" placeholder="Escribe tu respuesta técnica o solicita información..." required></textarea>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="private" id="privateNote" value="1">
                                <label class="form-check-label text-muted fw-bold small" for="privateNote">Ocultar al usuario (Nota Privada)</label>
                            </div>
                            <button type="submit" class="btn text-white fw-bold px-4" style="background-color: #F4A637;">
                                <i class="fas fa-paper-plane me-2"></i> Enviar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel Derecho: Acciones de TI -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white p-3">
                    <h6 class="fw-bold mb-0 text-muted"><i class="fas fa-tools me-2"></i>Gestión del Ticket</h6>
                </div>
                <div class="card-body p-3">
                    <!-- Formulario para Cerrar el Ticket -->
                    <form method="POST" action="{{ route('tickets.update', $ticket) }}">
                        @csrf @method('PUT')
                        <label class="small fw-bold text-muted mb-2">Estatus Actual</label>
                        <select name="status" class="form-select border-0 bg-light mb-3">
                            <option value="1" {{ $ticket->status == 1 ? 'selected' : '' }}>Abierto</option>
                            <option value="2" {{ $ticket->status == 2 ? 'selected' : '' }}>En Proceso</option>
                            <option value="3" {{ $ticket->status == 3 ? 'selected' : '' }}>Resuelto</option>
                        </select>
                        <button type="submit" class="btn btn-outline-success w-100 fw-bold">Actualizar Estatus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection