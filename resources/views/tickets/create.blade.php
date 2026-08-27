@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            {{-- Encabezado: Máquina Tragamonedas de Dulces --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; background-color: #67768A;">
                <div class="card-body text-center py-4">
                    <h5 class="text-white fw-bold mb-3" style="letter-spacing: 1px;">NUEVO REQUERIMIENTO</h5>
                    
                    <div id="slot-display" class="d-inline-flex align-items-center justify-content-center bg-white shadow-sm" 
                         style="font-size: 2.5rem; height: 80px; min-width: 200px; border-radius: 12px; letter-spacing: 8px; color: #F4A637; transition: all 0.3s ease;">
                        🍬🍭🍫
                    </div>
                </div>
            </div>

            {{-- Formulario Principal --}}
            <div class="card shadow-lg border-0" style="border-radius: 16px;">
                <div class="card-body p-4 p-md-5">
                    <form id="ticketForm" method="POST" action="{{ route('tickets.store') }}">
                        @csrf
                        
                        <h6 class="fw-bold mb-3" style="color: #67768A;">Datos del Solicitante</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">NOMBRE COMPLETO</label>
                                <input type="text" name="requester[name]" class="form-control form-control-lg bg-light border-0" placeholder="Ej. Juan Pérez" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">CORREO ELECTRÓNICO</label>
                                <input type="email" name="requester[email]" class="form-control form-control-lg bg-light border-0" placeholder="juan@tuempresa.com" required>
                            </div>
                        </div>

                        <hr class="text-muted opacity-25 mb-4">

                        <h6 class="fw-bold mb-3" style="color: #67768A;">Detalles del Ticket</h6>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">ASUNTO</label>
                            <input type="text" name="title" class="form-control form-control-lg bg-light border-0" placeholder="Breve descripción del problema" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">DESCRIPCIÓN DETALLADA</label>
                            <textarea name="body" class="form-control form-control-lg bg-light border-0" rows="4" placeholder="Explica a detalle el requerimiento..." required></textarea>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">ETIQUETAS</label>
                                <input type="text" name="tags" id="tags" class="form-control form-control-lg bg-light border-0" placeholder="ej. red, impresora">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">ESTADO INICIAL</label>
                                <select name="status" class="form-select form-select-lg bg-light border-0">
                                    <option value="1">Nuevo / Abierto</option>
                                    <option value="2">Pendiente</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">ASIGNAR A EQUIPO</label>
                                <select name="team_id" class="form-select form-select-lg bg-light border-0">
                                    <option value="">Sin asignar</option>
                                    {{-- Aquí iterarás los equipos cuando los conectemos a la BD --}}
                                </select>
                            </div>
                        </div>

                        <button type="submit" id="btn-submit" class="btn w-100 py-3 mt-2 text-white fw-bold shadow-sm" 
                                style="background-color: #F4A637; border-radius: 12px; font-size: 1.1rem; letter-spacing: 1px; transition: 0.3s;">
                            <i class="fas fa-paper-plane me-2"></i> GENERAR TICKET
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Efecto borroso rápido para la animación del casino */
    .slot-blur {
        animation: spinBlur 0.05s infinite alternate;
    }
    @keyframes spinBlur {
        0% { filter: blur(0px); transform: translateY(0); }
        100% { filter: blur(3px); transform: translateY(-4px); }
    }
    /* Limpieza visual de inputs al hacer foco */
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 3px rgba(244, 166, 55, 0.25);
        background-color: #ffffff !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('ticketForm');
        const btn = document.getElementById('btn-submit');
        const display = document.getElementById('slot-display');
        
        // Emojis de dulces para la animación
        const dulces = ["🍬", "🍭", "🍫", "🍩", "🧁", "🍪", "🍧", "🍨"];

        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Pausamos el envío real para hacer la animación

            // Bloquear botón y cambiar estado
            btn.disabled = true;
            btn.innerHTML = "¡SORTEANDO TICKET!...";
            btn.style.backgroundColor = "#67768A"; // Cambia al Azul Pizarra
            
            display.classList.add("slot-blur");

            // Efecto de giro en el display superior
            let spinInterval = setInterval(() => {
                let randomStr = "";
                for (let i = 0; i < 3; i++) {
                    randomStr += dulces[Math.floor(Math.random() * dulces.length)];
                }
                display.innerText = randomStr;
            }, 40);

            // Detener la animación y enviar los datos después de 1.5 segundos
            setTimeout(() => {
                clearInterval(spinInterval);
                display.classList.remove("slot-blur");
                
                // Mostrar éxito visual
                display.innerText = "✅🎫✅";
                btn.innerHTML = "¡CREADO CON ÉXITO!";
                btn.style.backgroundColor = "#10b981"; // Verde éxito

                // Enviar el formulario real al backend
                setTimeout(() => {
                    form.submit();
                }, 500);

            }, 1500);
        });
    });
</script>
@endpush