@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0" style="border-radius: 12px; border-top: 5px solid #F4A637;">
                <div class="card-body p-5">
                    <h4 class="fw-bold mb-4 text-center">
                        <i class="fas fa-lock text-warning me-2"></i> Actualiza tu Contraseña
                    </h4>
                    
                    <p class="text-muted text-center mb-4">
                        Por seguridad, debes cambiar tu contraseña predeterminada (12345) antes de poder utilizar el sistema.
                    </p>

                    <form method="POST" action="{{ route('password.force-update') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold">Nueva Contraseña</label>
                            <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" required placeholder="Mínimo 5 caracteres">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Confirmar Nueva Contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control form-control-lg" required placeholder="Ingrésala de nuevo">
                        </div>

                        <button type="submit" class="btn w-100 py-3 text-white fw-bold shadow-sm" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-radius: 8px;">
                            <i class="fas fa-save me-2"></i> Guardar y Continuar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
