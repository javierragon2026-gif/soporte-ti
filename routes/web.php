<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\RequesterTicketsController;
use App\Http\Controllers\RequesterCommentsController;

use App\Http\Controllers\TicketsController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\AttachmentsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ReportsController;

Route::redirect('/', '/login');

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación Manuales
|--------------------------------------------------------------------------
*/
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

/*
|--------------------------------------------------------------------------
| ZONA GENERAL (Accesible para Usuarios Finales y Sistemas)
|--------------------------------------------------------------------------
*/
// ¡LA SOLUCIÓN! Esta ruta debe ser pública para que cualquiera guarde tickets
Route::post('tickets', [TicketsController::class, 'store'])->name('tickets.store');

Route::group(['middleware' => ['auth']], function () {

    Route::get('perfil', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('contrasena', [ProfileController::class, 'password'])->name('profile.password');

    // Forzar cambio de contraseña
    Route::get('cambiar-contrasena', [ProfileController::class, 'forcePasswordForm'])->name('password.force-change')->withoutMiddleware([\App\Http\Middleware\ForcePasswordChange::class]);
    Route::post('cambiar-contrasena', [ProfileController::class, 'forcePasswordUpdate'])->name('password.force-update')->withoutMiddleware([\App\Http\Middleware\ForcePasswordChange::class]);

    Route::get('mis-tickets', [RequesterTicketsController::class, 'index'])->name('cliente.tickets.index');

    Route::group(['prefix' => 'cliente'], function () {
        Route::get('tickets/crear', [TicketsController::class, 'crearCliente'])->name('cliente.tickets.crear');
        Route::get('tickets/nuevo', [TicketsController::class, 'formularioCliente'])->name('cliente.tickets.formulario');

        Route::get('tickets/{ticket}', [RequesterTicketsController::class, 'show'])->name('cliente.tickets.show');
        Route::post('tickets/{ticket}/comentarios', [RequesterCommentsController::class, 'store'])->name('requester.comments.store');
        
        // Home Office Cliente
        Route::get('home-office', [\App\Http\Controllers\Cliente\HomeOfficeController::class, 'index'])->name('cliente.home-office.index');
        Route::get('home-office/solicitar', [\App\Http\Controllers\Cliente\HomeOfficeController::class, 'create'])->name('cliente.home-office.create');
        Route::post('home-office', [\App\Http\Controllers\Cliente\HomeOfficeController::class, 'store'])->name('cliente.home-office.store');
    });
});

/*
|--------------------------------------------------------------------------
| ZONA PRIVADA DE SISTEMAS (Solo Administradores de TI)
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth', \App\Http\Middleware\IsAdmin::class]], function () {

    Route::get('/welcome', function () {
        return view('welcome');
    })->name('dashboard');

    Route::post('/sync-users', function () {
        \Illuminate\Support\Facades\Artisan::call('sync:m365-users');
        return redirect()->back()->with('success', '¡Directorio de usuarios sincronizado con Microsoft 365!');
    })->name('admin.sync-users');
    
    // Le quitamos 'store' al resource porque ya lo declaramos arriba para todos
    Route::resource('tickets', TicketsController::class)->except(['store', 'edit']);

    Route::post('tickets/{ticket}/comentarios', [CommentsController::class, 'store'])->name('tickets.comments.store');
    Route::post('tickets/{ticket}/reabrir', [TicketsController::class, 'reopen'])->name('tickets.reopen');

    Route::get('adjuntos/{filename}', [AttachmentsController::class, 'show'])->name('attachments');

    Route::resource('usuarios', UsersController::class)->parameters(['usuarios' => 'user'])->only(['index', 'destroy', 'create', 'edit', 'update'])->names('users');
    Route::post('usuarios/guardar', [UsersController::class, 'store'])->name('user.store');
    Route::post('usuarios/{user}/reset-password', [UsersController::class, 'resetPasswordToDefault'])->name('users.reset-password');
    Route::get('usuarios/{user}/suplantar', [UsersController::class, 'impersonate'])->name('users.impersonate');

    Route::get('estadisticas', [ReportsController::class, 'analytics'])->name('reports.analytics');
    
    // Home Office y Control de Equipos - Admin
    Route::resource('inventario-equipos', \App\Http\Controllers\Admin\DeviceController::class)->names('admin.devices');
    
    Route::get('home-office', [\App\Http\Controllers\Admin\HomeOfficeController::class, 'index'])->name('admin.home-office.index');
    Route::get('home-office/{homeOffice}', [\App\Http\Controllers\Admin\HomeOfficeController::class, 'show'])->name('admin.home-office.show');
    Route::get('home-office/{homeOffice}/editar', [\App\Http\Controllers\Admin\HomeOfficeController::class, 'edit'])->name('admin.home-office.edit');
    Route::put('home-office/{homeOffice}', [\App\Http\Controllers\Admin\HomeOfficeController::class, 'update'])->name('admin.home-office.update');
    Route::delete('home-office/{homeOffice}', [\App\Http\Controllers\Admin\HomeOfficeController::class, 'destroy'])->name('admin.home-office.destroy');
    Route::post('home-office/{homeOffice}/aprobar', [\App\Http\Controllers\Admin\HomeOfficeController::class, 'approve'])->name('admin.home-office.approve');
    Route::post('home-office/{homeOffice}/checkin', [\App\Http\Controllers\Admin\HomeOfficeController::class, 'checkin'])->name('admin.home-office.checkin');
});