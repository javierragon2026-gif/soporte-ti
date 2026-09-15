@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h3 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">
            <i class="fas fa-chart-pie me-2" style="color: #F4A637;"></i> Métricas y Rendimiento TI
        </h3>
        
        <!-- Formulario de Rango de Fechas y Filtros -->
        <form method="GET" action="{{ route('reports.analytics') }}" class="d-flex flex-wrap align-items-center bg-white p-2 shadow-sm rounded-3 gap-2">
            <div class="d-flex align-items-center">
                <input type="date" name="start_date" class="form-control form-control-sm border-0 bg-light" value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                <span class="text-muted small mx-2">hasta</span>
                <input type="date" name="end_date" class="form-control form-control-sm border-0 bg-light" value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
            </div>
            
            <div class="border-start ps-2 ms-1">
                <select name="agent_id" class="form-select form-select-sm border-0 bg-light fw-bold text-secondary">
                    <option value="">Todos los Agentes</option>
                    @foreach($allAgents as $agent)
                        <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="btn btn-sm text-white fw-bold px-3 ms-1" style="background-color: #61b0a5;">
                <i class="fas fa-filter"></i>
            </button>
            <button type="submit" name="export" value="csv" class="btn btn-sm btn-outline-success fw-bold px-3 ms-1" title="Exportar a Excel">
                <i class="fas fa-file-excel"></i>
            </button>
        </form>
    </div>
    
    <div class="mb-3 text-muted fw-semibold">
        <i class="far fa-calendar-alt me-1"></i> Mostrando datos: <span class="text-dark">{{ $periodLabel }}</span>
        @if($selectedAgent)
            <span class="ms-2 badge bg-secondary"><i class="fas fa-user me-1"></i> Agente Filtrado</span>
        @endif
    </div>

    <!-- Tarjetas de Resumen Rápido -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 5px solid #e74c3c !important;">
                <div class="card-body">
                    <h6 class="text-muted fw-bold mb-1"><i class="fas fa-folder-open me-1"></i> Tickets Abiertos</h6>
                    <h2 class="fw-bold mb-0 text-dark">{{ $totalOpen }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 5px solid #2ecc71 !important;">
                <div class="card-body">
                    <h6 class="text-muted fw-bold mb-1"><i class="fas fa-check-circle me-1"></i> Tickets Cerrados</h6>
                    <h2 class="fw-bold mb-0 text-dark">{{ $totalClosed }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 5px solid #9b59b6 !important;">
                <div class="card-body">
                    <h6 class="text-muted fw-bold mb-1"><i class="fas fa-stopwatch me-1"></i> MTTR (Promedio)</h6>
                    <h2 class="fw-bold mb-0 text-dark">{{ $mttrHours }} <span class="fs-6 text-muted">hrs</span></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 5px solid #3498db !important;">
                <div class="card-body">
                    <h6 class="text-muted fw-bold mb-1"><i class="fas fa-box me-1"></i> Stock Laptops (Libres)</h6>
                    <h2 class="fw-bold mb-0 text-dark">{{ $invAvailable }} <span class="fs-6 text-muted">/ {{ $invAvailable + $invLoaned }}</span></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficas Principales -->
    <div class="row g-4">
        <!-- Gráfica de Barras: Rendimiento por Agente -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold text-dark"><i class="fas fa-trophy me-2" style="color: #f1c40f;"></i> Tickets Resueltos por Agente (Mes)</h5>
                </div>
                <div class="card-body">
                    <canvas id="agentsChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfica de Pastel: Distribución de Módulos -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold text-dark"><i class="fas fa-chart-pie me-2" style="color: #9b59b6;"></i> Impacto por Módulo</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <canvas id="categoryChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <!-- Gráfica de Prioridades -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold text-dark"><i class="fas fa-exclamation-triangle me-2" style="color: #e74c3c;"></i> Tickets por Prioridad</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <canvas id="priorityChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. Gráfica de Agentes (Barras)
        const ctxAgents = document.getElementById('agentsChart').getContext('2d');
        const agentNames = {!! $agentNames !!};
        const agentScores = {!! $agentScores !!};
        
        new Chart(ctxAgents, {
            type: 'bar',
            data: {
                labels: agentNames,
                datasets: [{
                    label: 'Tickets Resueltos',
                    data: agentScores,
                    backgroundColor: 'rgba(97, 176, 165, 0.7)',
                    borderColor: 'rgba(97, 176, 165, 1)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // 2. Gráfica de Categorías (Doughnut)
        const ctxCategory = document.getElementById('categoryChart').getContext('2d');
        const catNames = {!! $catNames !!};
        const catCounts = {!! $catCounts !!};
        
        new Chart(ctxCategory, {
            type: 'doughnut',
            data: {
                labels: catNames,
                datasets: [{
                    data: catCounts,
                    backgroundColor: [
                        '#e74c3c', // Rojo (SAP?)
                        '#3498db', // Azul
                        '#f1c40f', // Amarillo
                        '#2ecc71', // Verde
                        '#9b59b6', // Morado
                        '#34495e'  // Oscuro
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // 3. Gráfica de Prioridades (Bar o Pie)
        const ctxPriority = document.getElementById('priorityChart').getContext('2d');
        const priNames = {!! $priNames ?? '[]' !!};
        const priCounts = {!! $priCounts ?? '[]' !!};
        
        new Chart(ctxPriority, {
            type: 'bar',
            data: {
                labels: priNames,
                datasets: [{
                    label: 'Volumen',
                    data: priCounts,
                    backgroundColor: [
                        'rgba(52, 152, 219, 0.7)', // Baja - Azul
                        'rgba(241, 196, 15, 0.7)', // Normal - Amarillo
                        'rgba(231, 76, 60, 0.7)'   // Alta - Rojo
                    ],
                    borderColor: [
                        '#3498db',
                        '#f1c40f',
                        '#e74c3c'
                    ],
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endpush
