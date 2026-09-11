@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold" style="color: #67768A;">
                    <i class="fas fa-laptop-house me-2" style="color: #F4A637;"></i> Solicitar Equipo / Pase de Salida
                </h4>
                <a href="{{ route('cliente.home-office.index') }}" class="btn btn-outline-secondary btn-sm fw-bold">
                    <i class="fas fa-arrow-left me-1"></i> Volver al Historial
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger shadow-sm">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold mb-0 text-dark">Formulario de Solicitud (Home Office)</h6>
                </div>
                <div class="card-body p-4 bg-light">
                    <form method="POST" action="{{ route('cliente.home-office.store') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">¿Qué necesitas?</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check form-check-inline card shadow-sm w-100 p-3 h-100" style="border: 2px solid transparent;" onclick="this.querySelector('input').click()">
                                        <input class="form-check-input ms-1" type="radio" name="request_type" id="type_loan" value="shared_loan" checked required>
                                        <label class="form-check-label fw-bold ms-2" for="type_loan">
                                            <i class="fas fa-laptop me-1 text-primary"></i> Préstamo de Equipo
                                            <span class="d-block small text-muted fw-normal mt-1">Sistemas me prestará una laptop para llevar a casa.</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-check-inline card shadow-sm w-100 p-3 h-100" style="border: 2px solid transparent;" onclick="this.querySelector('input').click()">
                                        <input class="form-check-input ms-1" type="radio" name="request_type" id="type_pass" value="assigned_gate_pass" required>
                                        <label class="form-check-label fw-bold ms-2" for="type_pass">
                                            <i class="fas fa-door-open me-1 text-warning"></i> Pase de Salida
                                            <span class="d-block small text-muted fw-normal mt-1">Me llevaré el equipo que ya tengo asignado en la oficina.</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php
                            $startDate = \Carbon\Carbon::now();
                            $hoDate = \Carbon\Carbon::now()->addWeekdays(1);
                            $endDate = \Carbon\Carbon::now()->addWeekdays(2);
                        @endphp
                        
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">1. Fecha de Petición / Salida</label>
                                <input type="date" class="form-control bg-light text-muted border-0 shadow-sm" name="scheduled_start_date" value="{{ $startDate->format('Y-m-d') }}" readonly>
                                <span class="d-block small text-muted mt-1">El equipo se pide hoy mismo.</span>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-primary small text-uppercase">2. Día de Home Office</label>
                                <input type="date" class="form-control bg-light text-primary fw-bold border-0 shadow-sm" value="{{ $hoDate->format('Y-m-d') }}" readonly>
                                <span class="d-block small text-muted mt-1">Día asignado para trabajar en casa.</span>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">3. Fecha de Regreso</label>
                                <input type="date" class="form-control bg-light text-danger fw-bold border-0 shadow-sm" name="scheduled_end_date" value="{{ $endDate->format('Y-m-d') }}" readonly>
                                <span class="d-block small text-danger mt-1">Obligatoria y sin excepciones.</span>
                            </div>
                            
                            <div class="col-12 mt-3">
                                <div class="p-3 rounded text-danger" style="background-color: #fef2f2; border-left: 4px solid #ef4444;">
                                    <i class="fas fa-exclamation-triangle me-2"></i><strong>Atención:</strong> La fecha de regreso debe cumplirse <u>sin excepciones</u>. La entrega del equipo debe realizarse a primera hora del día <strong>{{ $endDate->format('d/m/Y') }}</strong>; de lo contrario, el sistema alertará como equipo atrasado.
                                </div>
                            </div>
                        </div>

                        <!-- RESPONSIVA DIGITAL -->
                        <div class="alert alert-secondary mt-4 mb-4" style="border-left: 4px solid #61b0a5;">
                            <h6 class="fw-bold text-dark"><i class="fas fa-file-signature me-1 text-success"></i> Responsiva Digital de Equipo de Cómputo</h6>
                            <p class="small text-muted mb-2">
                                Al marcar la casilla de abajo y procesar esta solicitud, confirmas bajo tu nombre que:
                            </p>
                            <ul class="small text-muted mb-3">
                                <li>Te haces responsable por el cuidado, resguardo y buen uso del equipo de cómputo y sus accesorios durante el periodo establecido.</li>
                                <li>El equipo será utilizado única y exclusivamente para fines laborales.</li>
                                <li>Devolverás el equipo a Sistemas (TI) el día <strong>{{ $endDate->format('d/m/Y') }}</strong> en las mismas condiciones en que fue entregado.</li>
                            </ul>
                            
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="policy_accepted" id="policy_accepted" value="1" required style="width: 1.2rem; height: 1.2rem;">
                                <label class="form-check-label ms-2 fw-bold text-dark" for="policy_accepted" style="padding-top: 2px;">
                                    He leído, entiendo y acepto las condiciones de la responsiva.
                                </label>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn text-white fw-bold py-3 shadow-sm" style="background-color: #61b0a5; font-size: 1.1rem; border-radius: 8px;">
                                <i class="fas fa-paper-plane me-2"></i> Enviar Solicitud a TI
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<style>
    .form-check.card:hover {
        border-color: #61b0a5 !important;
        cursor: pointer;
    }
    input[type="radio"]:checked + label {
        color: #61b0a5 !important;
    }
</style>
@endpush
@endsection

