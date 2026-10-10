@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Directorio de Usuarios ( {{ $users->total() }} )</h3>
        
        <form action="{{ route('users.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Buscar por nombre o correo..." value="{{ request('search') }}" style="min-width: 250px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary ms-2" title="Limpiar filtro">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Nombre / Display Name</th>
                        <th>Correo Electrónico</th>
                        <th class="text-center">Rol</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="ps-4 text-muted">#{{ $user->id }}</td>
                            <td class="fw-bold text-primary">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td class="text-center">
                                @if($user->admin)
                                    <span class="badge bg-danger">TI / Admin</span>
                                @else
                                    <span class="badge bg-secondary">Usuario</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Editar / Sincronizar"> 
                                    <i class="fas fa-pencil-alt"></i> 
                                </a>
                                <a href="{{ route('users.impersonate', $user) }}" class="btn btn-sm btn-outline-warning" title="Suplantar Sesión"> 
                                    <i class="fas fa-key"></i> 
                                </a>
                                <form action="{{ route('users.reset-password', $user) }}" method="POST" class="d-inline form-reset-password">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-info" title="Resetear a 12345">
                                        <i class="fas fa-unlock-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="mt-4 d-flex justify-content-center">
        {{ $users->links() }}
    </div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const resetForms = document.querySelectorAll('.form-reset-password');
    resetForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = form.querySelector('button');
            Swal.fire({
                title: '¿Resetear contraseña a 12345?',
                text: "El usuario será forzado a cambiarla en su próximo inicio de sesión.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, resetear',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Procesando...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    form.submit();
                } else {
                    // Si cancela y tenías un spinner o disableado el botón, lo restauramos
                    if(btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-unlock-alt"></i>';
                    }
                }
            });
        });
    });
});
</script>
@endsection
