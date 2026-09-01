@extends('layouts.app')

@section('content')
<div class="container py-5" style="background-color: #f4f6f9; min-height: 100vh;">
    <div class="row g-4 justify-content-center max-w-1000 mx-auto">
        
        <!-- Módulo: SAP -->
        <div class="col-md-6">
            <a href="{{ route('cliente.tickets.crear', ['categoria' => 'SAP']) }}" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm border-0 p-4 position-relative card-hover" style="border-radius: 8px;">

                    <div class="d-flex align-items-center">
                        <i class="fas fa-project-diagram fa-2x me-4" style="color: #61b0a5;"></i>
                        <div>
                            <h5 class="fw-bold mb-1">SAP</h5>
                            <p class="text-muted mb-0 small">Crear una solicitud en SAP</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Módulo: WMS -->
        <div class="col-md-6">
            <a href="{{ route('cliente.tickets.crear', ['categoria' => 'WMS']) }}" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm border-0 p-4 position-relative card-hover" style="border-radius: 8px;">

                    <div class="d-flex align-items-center">
                        <i class="fas fa-warehouse fa-2x me-4" style="color: #61b0a5;"></i>
                        <div>
                            <h5 class="fw-bold mb-1">WMS</h5>
                            <p class="text-muted mb-0 small">Crear una solicitud en WMS</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Módulo: Soporte -->
        <div class="col-md-6">
            <a href="{{ route('cliente.tickets.crear', ['categoria' => 'SOPORTE']) }}" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm border-0 p-4 position-relative card-hover" style="border-radius: 8px;">

                    <div class="d-flex align-items-center">
                        <i class="fas fa-headset fa-2x me-4" style="color: #61b0a5;"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Soporte</h5>
                            <p class="text-muted mb-0 small">Crear una solicitud en Soporte</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Módulo: Solicitudes Equipos -->
        <div class="col-md-6">
            <a href="{{ route('cliente.tickets.crear', ['categoria' => 'EQUIPOS']) }}" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm border-0 p-4 position-relative card-hover" style="border-radius: 8px;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-desktop fa-2x me-4" style="color: #61b0a5;"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Solicitudes Equipos</h5>
                            <p class="text-muted mb-0 small">Crear una solicitud de Infraestructura</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Módulo: Retail One -->
        <div class="col-md-6">
            <a href="{{ route('cliente.tickets.crear', ['categoria' => 'RETAIL']) }}" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm border-0 p-4 position-relative card-hover" style="border-radius: 8px;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-store fa-2x me-4" style="color: #8fa4b8;"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Retail One</h5>
                            <p class="text-muted mb-0 small">Crear una solicitud en Retail One</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Módulo: Otros -->
        <div class="col-md-6">
            <a href="{{ route('cliente.tickets.crear', ['categoria' => 'OTROS']) }}" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm border-0 p-4 position-relative card-hover" style="border-radius: 8px;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-alt fa-2x me-4" style="color: #8fa4b8;"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Otros</h5>
                            <p class="text-muted mb-0 small">Crear una solicitud en Otros</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>
</div>

<style>
    .card-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.1)!important;
    }
    .max-w-1000 {
        max-width: 1000px;
    }
</style>
@endsection