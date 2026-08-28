@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card shadow-lg border-0 overflow-hidden" style="border-radius: 16px; min-height: 500px;">

                    {{-- Encabezado Fijo --}}
                    <div class="card-header border-0 text-center py-4" style="background-color: #67768A;">
                        <h5 class="text-white fw-bold mb-0" style="letter-spacing: 1px;">
                            <i class="fas fa-ticket-alt me-2" style="color: #F4A637;"></i> MESA DE AYUDA
                        </h5>

                        <div class="header-actions mt-4">
                            <a href="{{ route('dashboard') }}" class="btn-ragon-outline">
                                <i class="fas fa-th-large me-2"></i> PANEL DE CONTROL
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5 position-relative">

                        {{-- 1. FORMULARIO PRINCIPAL --}}
                        <form id="ticketForm" method="POST" action="{{ route('tickets.store') }}">
                            @csrf

                            <h6 class="fw-bold mb-3" style="color: #67768A;">Datos del Solicitante</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">NOMBRE COMPLETO</label>
                                    <input type="text" name="requester[name]"
                                        class="form-control form-control-lg bg-light border-0" placeholder="Ej. Juan Pérez"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">CORREO ELECTRÓNICO</label>
                                    <input type="email" name="requester[email]"
                                        class="form-control form-control-lg bg-light border-0"
                                        placeholder="juan@tuempresa.com" required>
                                </div>
                            </div>

                            <hr class="text-muted opacity-25 mb-4">

                            <h6 class="fw-bold mb-3" style="color: #67768A;">Detalles del Ticket</h6>
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">ASUNTO</label>
                                <input type="text" name="title" class="form-control form-control-lg bg-light border-0"
                                    placeholder="Breve descripción del problema" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">DESCRIPCIÓN DETALLADA</label>
                                <textarea name="body" class="form-control form-control-lg bg-light border-0" rows="4"
                                    placeholder="Explica a detalle el requerimiento..." required></textarea>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-bold">ETIQUETAS</label>
                                    <input type="text" name="tags" id="tags"
                                        class="form-control form-control-lg bg-light border-0"
                                        placeholder="ej. red, impresora">
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
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn w-100 py-3 mt-3 text-white fw-bold shadow-lg slot-btn"
                                style="background-color: #F4A637; border-radius: 12px; font-size: 1.2rem; letter-spacing: 2px;">
                                <i class="fas fa-play me-2"></i> SOLICITAR TICKET
                            </button>
                        </form>

                        {{-- 2. PANTALLA DE CASINO (Oculta por defecto) --}}
                        <div id="casino-overlay"
                            class="d-none flex-column justify-content-center align-items-center text-center h-100 py-5">
                            <h4 class="fw-bold mb-4" style="color: #67768A; letter-spacing: 2px;">PROCESANDO
                                REQUERIMIENTO...</h4>

                            <div id="big-slot-display"
                                class="bg-white shadow-sm fw-bold d-flex align-items-center justify-content-center"
                                style="font-size: 4.5rem; height: 140px; width: 100%; max-width: 400px; border-radius: 20px; letter-spacing: 15px; color: #0f172a; border: 6px solid #e2e8f0; transition: all 0.4s ease;">
                                🎰🎲🎰
                            </div>

                            <h5 id="slot-message" class="fw-bold mt-5"
                                style="color: #F4A637; font-size: 1.5rem; animation: pulseText 0.8s infinite alternate;">
                                ¡Girando rodillos!
                            </h5>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <style>
        /* Efecto de velocidad en los dulces */
        .slot-blur {
            animation: slotBlur 0.05s infinite alternate;
            color: #64748b !important;
        }

        /* Estilo de ¡BINGO! / Éxito */
        .slot-success {
            border-color: #10b981 !important;
            color: #10b981 !important;
            background: #f0fdf4 !important;
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.4) !important;
            transform: scale(1.05);
        }

        @keyframes slotBlur {
            0% {
                filter: blur(0px);
                transform: translateY(0);
            }

            100% {
                filter: blur(4px);
                transform: translateY(-5px);
            }
        }

        @keyframes pulseText {
            from {
                opacity: 0.6;
                transform: scale(0.98);
            }

            to {
                opacity: 1;
                transform: scale(1.02);
            }
        }

        /* Botón animado */
        .slot-btn {
            transition: all 0.2s ease;
        }

        .slot-btn:active {
            transform: translateY(4px);
            box-shadow: 0 0px 0px rgba(0, 0, 0, 0) !important;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 3px rgba(244, 166, 55, 0.25);
            background-color: #ffffff !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('ticketForm');
            const casinoOverlay = document.getElementById('casino-overlay');
            const display = document.getElementById('big-slot-display');
            const slotMessage = document.getElementById('slot-message');

            const dulces = ["🍬", "🍭", "🍫", "🍩", "🧁", "🍪", "🍧", "🍨", "🍒", "🍓"];

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // 1. Ocultamos el formulario y mostramos el casino
                form.classList.add('d-none');
                casinoOverlay.classList.remove('d-none');
                casinoOverlay.classList.add('d-flex');

                // 2. Iniciamos la animación de la máquina
                display.classList.add("slot-blur");

                let spinInterval = setInterval(() => {
                    let randomStr = "";
                    for (let i = 0; i < 3; i++) {
                        randomStr += dulces[Math.floor(Math.random() * dulces.length)];
                    }
                    display.innerText = randomStr;
                }, 40);

                // 3. Frenamos la máquina después de 2.5 segundos
                setTimeout(() => {
                    clearInterval(spinInterval);
                    display.classList.remove("slot-blur");
                    display.classList.add("slot-success");

                    // Generamos un número de ticket falso visualmente (el real lo da la BD)
                    const randomNum = String(Math.floor(Math.random() * 999)).padStart(3, '0');
                    display.innerText = `TK-${randomNum}`;

                    // Cambiamos el mensaje
                    slotMessage.innerText = "¡BINGO! TICKET GENERADO";
                    slotMessage.style.color = "#10b981";
                    slotMessage.style.animation = "none";

                    // 4. Enviamos los datos reales al backend
                    setTimeout(() => {
                        form.submit();
                    }, 1200);

                }, 2500);
            });
        });
    </script>
@endsection
