@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold" style="color: #67768A;">Gestión de Solicitud: HO-{{ str_pad($homeOffice->id, 4, '0', STR_PAD_LEFT) }}</h4>
        <a href="{{ route('admin.home-office.index') }}" class="btn btn-outline-secondary btn-sm fw-bold">Volver a la Bandeja</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success fw-bold"><i class="fas fa-check-circle me-1"></i> {{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul></div>
    @endif

    <div class="row g-4">
        <!-- Columna Izquierda: Datos del Colaborador -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-user-circle me-1" style="color: #F4A637;"></i> Datos de la Solicitud</h6>
                </div>
                <div class="card-body bg-light">
                    <ul class="list-group list-group-flush" style="border-radius: 8px;">
                        <li class="list-group-item bg-white border-0 mb-1">
                            <span class="d-block small text-muted fw-bold">COLABORADOR</span>
                            <span class="fs-6 fw-bold text-dark">{{ $homeOffice->user->name }}</span>
                        </li>
                        <li class="list-group-item bg-white border-0 mb-1">
                            <span class="d-block small text-muted fw-bold">TIPO DE SOLICITUD</span>
                            @if($homeOffice->request_type == 'shared_loan')
                                <span class="badge bg-primary">Préstamo de Equipo Compartido</span>
                            @else
                                <span class="badge bg-warning text-dark">Pase de Salida (Equipo Fijo)</span>
                            @endif
                        </li>
                        <li class="list-group-item bg-white border-0 mb-1">
                            <span class="d-block small text-muted fw-bold">PERIODO SOLICITADO</span>
                            <strong class="text-dark">{{ $homeOffice->scheduled_start_date->format('d/m/Y') }}</strong> al <strong class="text-dark">{{ $homeOffice->scheduled_end_date->format('d/m/Y') }}</strong>
                        </li>
                        <li class="list-group-item bg-white border-0">
                            <span class="d-block small text-muted fw-bold">RESPONSIVA DIGITAL</span>
                            @if($homeOffice->policy_accepted)
                                <span class="text-success fw-bold"><i class="fas fa-check-circle"></i> Aceptada digitalmente</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
            
            @php 
                $isOverdue = ($homeOffice->status === 'active' || $homeOffice->status === 'approved') && \Carbon\Carbon::parse($homeOffice->scheduled_end_date)->isPast() && !\Carbon\Carbon::parse($homeOffice->scheduled_end_date)->isToday(); 
            @endphp
            
            @if($isOverdue)
            <div class="card border-0 shadow-sm mt-4 bg-danger text-white" style="border-radius: 12px;">
                <div class="card-body p-3 text-center">
                    <h6 class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i> EQUIPO NO DEVUELTO A TIEMPO</h6>
                    <p class="small mb-0">La fecha límite de ingreso fue el {{ $homeOffice->scheduled_end_date->format('d/m/Y') }}. Contacta al colaborador.</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Columna Derecha: Flujo Operativo de TI -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-top: 4px solid #61b0a5 !important;">
                <div class="card-body p-4">
                    
                    <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-cogs me-2 text-secondary"></i> Acciones de TI</h5>

                    @if($homeOffice->status === 'pending')
                        <!-- PASO 1: APROBAR Y ASIGNAR -->
                        <div class="alert alert-warning border-0 shadow-sm">
                            <h6 class="fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> Pendiente de Aprobación y Salida</h6>
                            <p class="small mb-3">Revisa las fechas. Selecciona la laptop a asignar. Al aprobar, se marcará automáticamente la salida del equipo (junto con su cargador).</p>
                            
                            <form method="POST" action="{{ route('admin.home-office.approve', $homeOffice) }}">
                                @csrf
                                @if($homeOffice->request_type === 'shared_loan')
                                    <div class="mb-3">
                                        <label class="fw-bold small text-dark">SELECCIONAR EQUIPO DISPONIBLE</label>
                                        <select name="device_id" class="form-select border-0 shadow-sm mt-1" required>
                                            <option value="">-- Elige una Laptop --</option>
                                            @foreach($availableDevices as $d)
                                                <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->serial_number ?? 'Sin S/N' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <button type="submit" class="btn btn-success fw-bold shadow-sm w-100"><i class="fas fa-sign-out-alt me-1"></i> Aprobar y Registrar Salida</button>
                            </form>
                        </div>

                    @elseif($homeOffice->status === 'active')
                        <!-- PASO 3: CHECKIN (INGRESO/DEVOLUCIÓN) -->
                        <div class="alert alert-success border-0 shadow-sm" style="background-color: #dcfce7;">
                            <h6 class="fw-bold text-success"><i class="fas fa-sign-in-alt me-1"></i> Marcar Ingreso / Recibir Equipo</h6>
                            <p class="small text-dark mb-3">El equipo está actualmente fuera de la oficina. Llena este formulario cuando el colaborador te devuelva el equipo.</p>
                            
                            <!-- Resumen Salida -->
                            <div class="bg-white p-3 rounded shadow-sm mb-3">
                                <span class="d-block small text-muted fw-bold">SE ENTREGÓ CON:</span>
                                <ul class="small mb-1 mt-1 text-dark">
                                    @if($homeOffice->accessories)
                                        @foreach($homeOffice->accessories as $acc) <li>{{ $acc }}</li> @endforeach
                                    @else
                                        <li>Sin accesorios registrados</li>
                                    @endif
                                </ul>
                                @if($homeOffice->checkout_notes)
                                    <span class="d-block small text-muted fw-bold mt-2">NOTA DE SALIDA:</span>
                                    <span class="small text-dark fst-italic">{{ $homeOffice->checkout_notes }}</span>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('admin.home-office.checkin', $homeOffice) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="fw-bold small text-dark">OBSERVACIONES DE INGRESO (¿FALTÓ ALGO? ¿DAÑOS?)</label>
                                    <textarea name="checkin_notes" class="form-control border-0 shadow-sm mt-1" rows="2" placeholder="Ej: Se entregó completo y funcionando..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-success fw-bold shadow-sm w-100"><i class="fas fa-check-double me-1"></i> Recibir y Finalizar Préstamo</button>
                            </form>
                        </div>

                    @elseif($homeOffice->status === 'returned')
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                            <h5 class="fw-bold text-dark mt-3">Proceso Completado</h5>
                            <p class="text-muted small">Este equipo fue devuelto correctamente el {{ $homeOffice->checkin_at->format('d/m/Y H:i') }} por {{ $homeOffice->checkinAgent->name ?? 'TI' }}.</p>
                            @if($homeOffice->checkin_notes)
                                <div class="bg-light p-3 rounded text-start mt-3 border">
                                    <span class="d-block small text-muted fw-bold">NOTA DE DEVOLUCIÓN:</span>
                                    <span class="text-dark small">{{ $homeOffice->checkin_notes }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

