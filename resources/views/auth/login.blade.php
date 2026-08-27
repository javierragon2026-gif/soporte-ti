@extends('auth.layout')

@section('content')
    {{-- Contenedor fijo para escapar de los márgenes del layout y abarcar el 100% de la pantalla sin huecos --}}
    <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; overflow: hidden; display: flex;">
        <div class="row g-0 w-100 h-100">
            
            {{-- Columna Izquierda: Fondo Naranja (Difuminado) con Partículas Grises/Azules --}}
            <div class="col-md-7 col-lg-8 d-none d-md-block position-relative" 
                 style="background: linear-gradient(135deg, #FFF8F0 0%, #FFDCA8 100%);">
                <div id="canvas-container" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; z-index: 1;"></div>
            </div>

            {{-- Columna Derecha: Panel de Login Modo Oscuro Corporativo --}}
            <div class="col-md-5 col-lg-4 d-flex align-items-center justify-content-center shadow-lg h-100" 
                 style="background-color: #67768A; z-index: 2;">
                 
                <div class="w-100 p-4 p-md-5" style="max-width: 450px;">
                    
                    {{-- Logo visible solo en dispositivos móviles --}}
                    <div class="text-center d-md-none mb-4">
                        <img src="{{ asset('img/logo.png') }}" alt="Logotipo de Soporte" class="img-fluid" style="max-height: 100px; object-fit: contain;">
                    </div>

                    {{-- Título y subtítulo en tonos blancos/claros --}}
                    <div class="text-center mb-5">
                        <h3 style="color: #ffffff; font-weight: 700; letter-spacing: 1px;">SOPORTE TI</h3>
                        <p style="font-size: 0.9rem; color: #D1D8E0;">Ingresa tus credenciales para acceder</p>
                    </div>

                    {{-- Formulario de inicio de sesión --}}
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- Campo de entrada para el Correo --}}
                        <div class="mb-4">
                            <label class="form-label-custom d-block" for="correo" style="color: #D1D8E0; font-weight: 600; font-size: 0.85rem;">CORREO ELECTRÓNICO</label>
                            
                            {{-- Input oscuro: borde muy sutil, fondo semitransparente --}}
                            <div class="input-group-modern" style="border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; background: rgba(0,0,0,0.15);">
                                <i class="fas fa-envelope icon" style="color: ; padding-left: 15px;"></i>
                                <input id="correo" type="email" name="email" class="form-input" 
                                       style="background: transparent; color: #ffffff; border: none; padding: 10px; width: 80%; outline: none;"
                                       value="{{ old('email') }}" placeholder="usuario@ragon.com.mx" required autofocus>
                            </div>
                            
                            @if ($errors->has('email'))
                                <span class="text-danger d-block mt-2" style="font-size: 0.85rem; color: #FFA8A8 !important;">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>

                        {{-- Campo de entrada para la Contraseña --}}
                        <div class="mb-4">
                            <label class="form-label-custom d-block" for="contrasena" style="color: #D1D8E0; font-weight: 600; font-size: 0.85rem;">CONTRASEÑA</label>
                            
                            <div class="input-group-modern" style="border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; background: rgba(0,0,0,0.15);">
                                <i class="fas fa-lock icon" style="color: #ffffff; padding-left: 15px;"></i>
                                <input id="contrasena" type="password" name="password" class="form-input" 
                                       style="background: transparent; color: #ffffff; border: none; padding: 10px; width: 80%; outline: none;"
                                       placeholder="••••••••" required>
                            </div>

                            @if ($errors->has('password'))
                                <span class="text-danger d-block mt-2" style="font-size: 0.85rem; color: #FFA8A8 !important;">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>

                        {{-- Casilla para recordar sesión --}}
                        <div class="mb-4 d-flex justify-content-between align-items-center">
                            <div>
                                <input type="checkbox" name="remember" id="recordarme" {{ old('remember') ? 'checked' : '' }}>
                                <label for="recordarme" style="font-size: 0.85rem; color: #D1D8E0; cursor: pointer; margin-left: 5px;">Recordarme</label>
                            </div>
                            {{-- Enlace en color naranja acento --}}
                            <a class="btn btn-link p-0" style="color: #F4A637; text-decoration: none; font-size: 0.85rem; font-weight: 600;" href="{{ route('password.request') }}">
                                ¿Olvidaste la clave?
                            </a>
                        </div>

                        {{-- Botón principal --}}
                        <button type="submit" class="btn-ragon-modern w-100" 
                                style="background-color: #F4A637; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; letter-spacing: 1px; transition: 0.3s; width: 100%;">
                            INICIAR SESIÓN
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script de Three.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('canvas-container');
            if (!container) return;

            const scene = new THREE.Scene();
            const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
            camera.position.z = 150;

            const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
            renderer.setSize(container.clientWidth, container.clientHeight);
            renderer.setPixelRatio(window.devicePixelRatio);
            container.appendChild(renderer.domElement);

            const mouse = new THREE.Vector2(-1000, -1000);
            const raycaster = new THREE.Raycaster();
            const plane = new THREE.Plane(new THREE.Vector3(0, 0, 1), 0);
            const pointOfIntersection = new THREE.Vector3();

            window.addEventListener('mousemove', (event) => {
                const rect = container.getBoundingClientRect();
                mouse.x = ((event.clientX - rect.left) / container.clientWidth) * 2 - 1;
                mouse.y = -((event.clientY - rect.top) / container.clientHeight) * 2 + 1;
                
                raycaster.setFromCamera(mouse, camera);
                raycaster.ray.intersectPlane(plane, pointOfIntersection);
            });

            const img = new Image();
            img.src = "{{ asset('img/logo.png') }}";

            img.onload = () => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                const width = 120;
                const height = 120 * (img.height / img.width);
                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);

                const imgData = ctx.getImageData(0, 0, width, height).data;
                const geometry = new THREE.BufferGeometry();
                const particles = [];
                const basePositions = [];

                for (let y = 0; y < height; y++) {
                    for (let x = 0; x < width; x++) {
                        const i = (y * width + x) * 4;
                        const alpha = imgData[i + 3];

                        if (alpha > 50) {
                            const pX = (x - width / 2) * 2.5;
                            const pY = -(y - height / 2) * 2.5;
                            const pZ = (Math.random() - 0.5) * 10;

                            particles.push(pX, pY, pZ);
                            basePositions.push(pX, pY, pZ);
                        }
                    }
                }

                geometry.setAttribute('position', new THREE.Float32BufferAttribute(particles, 3));
                geometry.setAttribute('basePosition', new THREE.Float32BufferAttribute(basePositions, 3));

                // Partículas usando el color Azul Pizarra de tu logo para contrastar con el naranja claro
                const material = new THREE.PointsMaterial({
                    color: 0x67768A, 
                    size: 1.5,
                    transparent: true,
                    opacity: 0.8
                });

                const particleSystem = new THREE.Points(geometry, material);
                scene.add(particleSystem);

                const animate = function() {
                    requestAnimationFrame(animate);

                    const positions = particleSystem.geometry.attributes.position.array;
                    const bases = particleSystem.geometry.attributes.basePosition.array;

                    for (let i = 0; i < positions.length; i += 3) {
                        const px = positions[i];
                        const py = positions[i + 1];
                        const bx = bases[i];
                        const by = bases[i + 1];

                        const dx = pointOfIntersection.x - px;
                        const dy = pointOfIntersection.y - py;
                        const dist = Math.sqrt(dx * dx + dy * dy);

                        if (dist < 30) {
                            const force = (30 - dist) / 30;
                            positions[i] -= (dx / dist) * force * 2.5;
                            positions[i + 1] -= (dy / dist) * force * 2.5;
                        } else {
                            positions[i] += (bx - px) * 0.05;
                            positions[i + 1] += (by - py) * 0.05;
                        }

                        positions[i + 2] += Math.sin(Date.now() * 0.001 + i) * 0.05;
                    }

                    particleSystem.geometry.attributes.position.needsUpdate = true;
                    
                    particleSystem.rotation.y = Math.sin(Date.now() * 0.0005) * 0.08;
                    particleSystem.rotation.x = Math.cos(Date.now() * 0.0005) * 0.04;

                    renderer.render(scene, camera);
                };

                animate();
            };

            window.addEventListener('resize', () => {
                if(container.clientWidth > 0) {
                    camera.aspect = container.clientWidth / container.clientHeight;
                    camera.updateProjectionMatrix();
                    renderer.setSize(container.clientWidth, container.clientHeight);
                }
            });
        });
    </script>
@endsection