@extends('layouts.app')
@section('content')
    <div class="description">
        <h3>Editar Usuario: {{ $user->name }}</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background-color: #d1fae5; padding: 15px; border-radius: 5px; color: #065f46; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 15px;">
                <label for="name" style="font-weight: bold; display: block; margin-bottom: 5px;">Nombre (DisplayName en M365)</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                @error('name')
                    <span style="color: red; font-size: 0.9em;">{{ $message }}</span>
                @enderror
                @if($user->azure_id)
                    <p style="font-size: 0.8em; color: #6b7280; margin-top: 5px;">Al guardar, este nombre se sincronizará automáticamente con Microsoft 365 porque el usuario está vinculado.</p>
                @endif
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Correo Electrónico (No editable)</label>
                <input type="email" value="{{ $user->email }}" disabled style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f3f4f6;">
            </div>

            <button type="submit" class="button" style="background-color: #3b82f6; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;">
                Guardar y Sincronizar
            </button>
            <a href="{{ route('users.index') }}" style="margin-left: 10px; color: #6b7280; text-decoration: none;">Cancelar</a>
        </form>
    </div>
@endsection
