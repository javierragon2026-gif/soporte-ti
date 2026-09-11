@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4 px-4">

        <!-- Encabezado y Botón de Nuevo -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold" style="color: #67768A;">
                <i class="fas fa-inbox me-2" style="color: #F4A637;"></i> Bandeja de Sistemas (TI)
            </h4>
            <a href="{{ route('tickets.create') }}" class="btn text-white fw-bold shadow-sm"
                style="background-color: #F4A637; border-radius: 8px;">
                <i class="fas fa-plus me-2"></i> Crear Ticket Interno
            </a>
        </div>

        <!-- Tarjeta de Filtros -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #ffffff;">
            <div class="card-body p-3 p-md-4">
                <form method="GET" action="{{ route('tickets.index') }}" class="row g-3 align-items-end">

                    {{-- FILA 1: Búsqueda y Clasificación --}}

                    <!-- Búsqueda General -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted"><i class="fas fa-search me-1"></i> Búsqueda
                            rápida</label>
                        <div class="input-group custom-search-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-search"
                                    style="color: #61b0a5;"></i></span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="form-control bg-light border-0 shadow-none" placeholder="Asunto, ID, Solicitante...">
                        </div>
                    </div>

                    <!-- Filtro Estado -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted"><i class="fas fa-tasks me-1"></i> Estado</label>
                        <select name="status" class="form-select bg-light border-0 shadow-none fw-semibold text-secondary">
                            <option value="">Todos los estados</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Nuevos / Abiertos
                            </option>
                            <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>En Proceso</option>
                            <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Pendientes (Falta Info)
                            </option>
                            <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Resueltos</option>
                            <option value="5" {{ request('status') == '5' ? 'selected' : '' }}>Cerrados (Histórico)
                            </option>
                        </select>
                    </div>

                    <!-- Filtro Categoría -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted"><i class="fas fa-tags me-1"></i>
                            Categoría</label>
                        <select name="categoria"
                            class="form-select bg-light border-0 shadow-none fw-semibold text-secondary">
                            <option value="">Todas las categorías</option>
                            <option value="SAP" {{ request('categoria') == 'SAP' ? 'selected' : '' }}>SAP</option>
                            <option value="WMS" {{ request('categoria') == 'WMS' ? 'selected' : '' }}>WMS</option>
                            <option value="SOPORTE" {{ request('categoria') == 'SOPORTE' ? 'selected' : '' }}>Soporte
                                Técnico</option>
                            <option value="EQUIPOS" {{ request('categoria') == 'EQUIPOS' ? 'selected' : '' }}>
                                Infraestructura</option>
                            <option value="RETAIL" {{ request('categoria') == 'RETAIL' ? 'selected' : '' }}>Retail One
                            </option>
                        </select>
                    </div>

                    <!-- Filtro Asignación -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted"><i class="fas fa-users me-1"></i>
                            Asignación</label>
                        <select name="team_id" class="form-select bg-light border-0 shadow-none fw-semibold text-secondary">
                            <option value="">Bandeja General / Todos</option>
                            {{-- Aquí iterarán los equipos cuando los conectes --}}
                        </select>
                    </div>

                    {{-- FILA 2: Fechas, Prioridad y Botones --}}

                    <!-- Rango de Fechas: Desde -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted"><i class="far fa-calendar-alt me-1"></i> Fecha
                            Desde</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="form-control bg-light border-0 shadow-none fw-semibold text-secondary">
                    </div>

                    <!-- Rango de Fechas: Hasta -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted"><i class="far fa-calendar-check me-1"></i> Fecha
                            Hasta</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="form-control bg-light border-0 shadow-none fw-semibold text-secondary">
                    </div>

                    <!-- Filtro Prioridad -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted"><i class="fas fa-exclamation-triangle me-1"></i>
                            Prioridad</label>
                        <select name="priority"
                            class="form-select bg-light border-0 shadow-none fw-semibold text-secondary">
                            <option value="">Todas las prioridades</option>
                            <option value="1" {{ request('priority') == '1' ? 'selected' : '' }}>Baja</option>
                            <option value="2" {{ request('priority') == '2' ? 'selected' : '' }}>Normal</option>
                            <option value="3" {{ request('priority') == '3' ? 'selected' : '' }}>Alta / Urgente
                            </option>
                        </select>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn text-white w-100 fw-bold shadow-sm"
                            style="background-color: #61b0a5; border-radius: 8px;">
                            <i class="fas fa-filter me-1"></i> Filtrar
                        </button>
                        {{-- Muestra el botón de limpiar solo si hay algún filtro activo --}}
                        @if (request()->hasAny(['search', 'status', 'categoria', 'team_id', 'date_from', 'date_to', 'priority']) &&
                                !empty(array_filter(request()->only(['search', 'status', 'categoria', 'team_id', 'date_from', 'date_to', 'priority']))
                                ))
                            <a href="{{ route('tickets.index') }}" class="btn btn-light px-3 fw-bold shadow-sm"
                                style="border-radius: 8px; color: #e74c3c;" title="Limpiar filtros">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Pestañas de Navegación Rápida -->
        <ul class="nav nav-tabs border-0 mb-3" id="inboxTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="{{ route('tickets.index') }}"
                    class="nav-link {{ !request('status') ? 'active fw-bold' : 'text-muted fw-semibold' }} border-0 px-4"
                    style="{{ !request('status') ? 'color: #F4A637; border-bottom: 3px solid #F4A637 !important; background: transparent;' : 'background: transparent;' }}">
                    Todos Activos
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('tickets.index', ['status' => 1]) }}"
                    class="nav-link {{ request('status') == '1' ? 'active fw-bold' : 'text-muted fw-semibold' }} border-0 px-4"
                    style="{{ request('status') == '1' ? 'color: #61b0a5; border-bottom: 3px solid #61b0a5 !important; background: transparent;' : 'background: transparent;' }}">
                    Recién Llegados
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('tickets.index', ['status' => 5]) }}"
                    class="nav-link {{ request('status') == '5' ? 'active fw-bold' : 'text-muted fw-semibold' }} border-0 px-4"
                    style="{{ request('status') == '5' ? 'color: #67768A; border-bottom: 3px solid #67768A !important; background: transparent;' : 'background: transparent;' }}">
                    <i class="fas fa-archive me-1"></i> Histórico Cerrados
                </a>
            </li>
        </ul>

        <!-- Tabla de Requerimientos -->
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px;">
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover align-middle mb-0 custom-table">
                    <thead
                        style="background-color: #f8fafc; color: #67768A; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th class="px-4 py-3 border-0">Ticket</th>
                            <th class="py-3 border-0">Solicitante</th>
                            <th class="py-3 border-0">Asunto</th>
                            <th class="py-3 border-0">Asignado A</th>
                            <th class="py-3 border-0">Estado</th>
                            <th class="py-3 border-0">Apertura</th>
                            <th class="px-4 py-3 border-0 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            {{-- Hacer toda la fila clickeable mediante Javascript --}}
                            <tr style="cursor: pointer;"
                                onclick="window.location='{{ route('tickets.show', $ticket) }}'">

                                <td class="px-4 fw-bold" style="color: #F4A637;">
                                    TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3 text-white d-flex align-items-center justify-content-center fw-bold rounded-circle shadow-sm"
                                            style="width: 36px; height: 36px; background-color: #67768A; font-size: 0.9rem;">
                                            {{ strtoupper(substr($ticket->user ? $ticket->user->name : 'S', 0, 1)) }}
                                        </div>
                                        <div class="text-dark fw-bold small">
                                            {{ $ticket->user ? $ticket->user->name : 'Usuario Desconocido' }}
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="text-dark fw-semibold d-block text-truncate" style="max-width: 250px;"
                                        title="{{ $ticket->title }}">
                                        {{ $ticket->title }}
                                    </span>
                                </td>

                                <td>
                                    <span
                                        class="badge bg-light text-secondary border border-secondary border-opacity-25 px-2 py-1 fw-bold shadow-sm">
                                        <i class="fas fa-users me-1" style="color: #61b0a5;"></i>
                                        {{ $ticket->team ? $ticket->team->name : 'Bandeja General' }}
                                    </span>
                                </td>

                                <td>
                                    @if ($ticket->status == 1)
                                        <span class="badge px-3 py-2 shadow-sm"
                                            style="background-color: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;">Abierto
                                            / Nuevo</span>
                                    @elseif($ticket->status == 2)
                                        <span class="badge px-3 py-2 shadow-sm"
                                            style="background-color: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe;">En
                                            Proceso</span>
                                    @elseif($ticket->status == 3)
                                        <span class="badge px-3 py-2 shadow-sm"
                                            style="background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a;">Pendiente</span>
                                    @elseif($ticket->status == 4)
                                        <span class="badge px-3 py-2 shadow-sm"
                                            style="background-color: #d1fae5; color: #047857; border: 1px solid #a7f3d0;">Resuelto</span>
                                    @else
                                        <span class="badge px-3 py-2 shadow-sm"
                                            style="background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;">Cerrado</span>
                                    @endif
                                </td>

                                <td class="text-muted small fw-semibold">
                                    <div class="d-flex flex-column">
                                        <span><i class="far fa-calendar-alt me-1 text-secondary"></i>
                                            {{ $ticket->created_at ? $ticket->created_at->format('d M, Y') : '--' }}</span>
                                        <span><i class="far fa-clock me-1 text-secondary"></i>
                                            {{ $ticket->created_at ? $ticket->created_at->format('H:i a') : '--' }}</span>
                                    </div>
                                </td>

                                <td class="px-4 text-end">
                                    {{-- Prevenimos la propagación del clic para que no choque con el clic de la fila --}}
                                    <a href="{{ route('tickets.show', $ticket) }}"
                                        class="btn btn-sm text-white fw-bold px-3 shadow-sm ticket-action-btn"
                                        style="background-color: #61b0a5; border-radius: 6px;"
                                        onclick="event.stopPropagation();">
                                        Gestionar <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state d-flex flex-column align-items-center justify-content-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm"
                                            style="width: 80px; height: 80px; background-color: #f0f4f8;">
                                            <i class="fas fa-inbox fa-3x" style="color: #cbd5e1;"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Bandeja Limpia</h5>
                                        <p class="text-muted">No se encontraron tickets con los filtros aplicados en este
                                            momento.</p>
                                        <a href="{{ route('tickets.index') }}"
                                            class="btn btn-outline-secondary btn-sm fw-bold rounded-pill px-4 mt-2 shadow-sm">Ver
                                            Todos los Tickets</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación nativa de Laravel --}}
            @if (method_exists($tickets, 'hasPages') && $tickets->hasPages())
                <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-end">
                    {{ $tickets->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <style>
        /* Efecto Hover sobre las filas de la tabla */
        .custom-table tbody tr {
            transition: all 0.2s ease;
        }

        .custom-table tbody tr:hover {
            background-color: #f8fafc !important;
            box-shadow: inset 4px 0 0 0 #F4A637;
            /* Raya lateral naranja corporativa */
            transform: scale(1.002);
        }

        /* Botón de acción al pasar el ratón */
        .ticket-action-btn {
            transition: all 0.2s ease;
        }

        .ticket-action-btn:hover {
            background-color: #4a8c84 !important;
            transform: translateX(-3px);
        }

        /* Estilos personalizados para el grupo de búsqueda */
        .custom-search-group {
            border-radius: 0.375rem;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .custom-search-group:focus-within {
            border: 1px solid #61b0a5;
            box-shadow: 0 0 0 3px rgba(97, 176, 165, 0.2);
            background-color: #ffffff !important;
        }

        .custom-search-group .input-group-text,
        .custom-search-group input {
            background-color: #f8f9fa !important;
        }

        .custom-search-group:focus-within .input-group-text,
        .custom-search-group:focus-within input {
            background-color: #ffffff !important;
        }

        /* Efectos Focus para los Selects */
        .form-select:focus {
            border: 1px solid #61b0a5 !important;
            box-shadow: 0 0 0 3px rgba(97, 176, 165, 0.2) !important;
            background-color: #ffffff !important;
        }
    </style>
@endsection
