<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/* 
|--------------------------------------------------------------------------
| Importación de Controladores
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

use App\Http\Controllers\ProfileController;

// Controladores para la vista del Usuario Final
use App\Http\Controllers\RequesterTicketsController;
use App\Http\Controllers\RequesterCommentsController;

// Controladores para la vista de Sistemas (Administradores)
use App\Http\Controllers\TicketsMergeController;
use App\Http\Controllers\TicketsSearchController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\TicketsAssignController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\TicketsTagsController;
use App\Http\Controllers\TicketsEscalateController;
use App\Http\Controllers\RequestersController;
use App\Http\Controllers\AttachmentsController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\TeamAgentsController;
use App\Http\Controllers\TeamMembershipController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TicketTypesController;
use App\Http\Controllers\ReportsController;

// Redirección directa al login
Route::redirect('/', '/login');

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación Manuales
|--------------------------------------------------------------------------
*/
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Rutas para recuperación de contraseña (públicas)
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

/*
|--------------------------------------------------------------------------
| ZONA GENERAL (Accesible para Usuarios Finales y Sistemas)
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth']], function () {

    // Perfil de Usuario
    Route::get('perfil', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('contrasena', [ProfileController::class, 'password'])->name('profile.password');

    // 1. Crear Ticket (Catálogo y Formulario)
    Route::get('cliente/tickets/crear', [TicketsController::class, 'crearCliente'])->name('cliente.tickets.crear');
    Route::get('cliente/tickets/nuevo', [TicketsController::class, 'formularioCliente'])->name('cliente.tickets.formulario');

    // 2. Histórico de tickets del usuario conectado
    Route::get('mis-tickets', [RequesterTicketsController::class, 'index'])->name('cliente.tickets.index');

    // 3. Seguimiento a detalle de un ticket específico
    Route::get('mis-tickets/{ticket}', [RequesterTicketsController::class, 'show'])->name('cliente.tickets.show');
});

/*
|--------------------------------------------------------------------------
| ZONA PRIVADA DE SISTEMAS (Protegida solo para Administradores de TI)
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth', \App\Http\Middleware\IsAdmin::class]], function () {

    // Esta ruta no puede estar suelta afuera del grupo
    Route::get('/welcome', function () {
        return view('welcome');
    })->name('dashboard');

    // Gestión de Tickets
    Route::get('tickets/fusionar', [TicketsMergeController::class, 'index'])->name('tickets.merge.index');
    Route::get('tickets/buscar/{text}', [TicketsSearchController::class, 'index'])->name('tickets.search');
    Route::resource('tickets', TicketsController::class)->except(['edit', 'destroy']);

    // Acciones específicas sobre un ticket
    Route::post('tickets/{ticket}/asignar', [TicketsAssignController::class, 'store'])->name('tickets.assign');
    Route::post('tickets/{ticket}/comentarios', [CommentsController::class, 'store'])->name('comments.store');
    Route::post('tickets/{ticket}/etiquetas', [TicketsTagsController::class, 'store'])->name('tickets.tags.store');
    Route::delete('tickets/{ticket}/etiquetas/{tag}', [TicketsTagsController::class, 'destroy'])->name('tickets.tags.destroy');
    Route::post('tickets/{ticket}/reabrir', [TicketsController::class, 'reopen'])->name('tickets.reopen');
    Route::post('tickets/{ticket}/escalar', [TicketsEscalateController::class, 'store'])->name('tickets.escalate.store');
    Route::delete('tickets/{ticket}/escalar', [TicketsEscalateController::class, 'destroy'])->name('tickets.escalate.destroy');

    // Directorio de Usuarios (Externos) y Adjuntos
    Route::get('clientes', [RequestersController::class, 'index'])->name('requesters.index');
    Route::get('adjuntos/{filename}', [AttachmentsController::class, 'show'])->name('attachments');
    Route::resource('tareas', TasksController::class)->only(['index', 'update', 'destroy']);

    // Gestión de Equipos de TI
    Route::resource('equipos', TeamsController::class)->names('teams');
    Route::get('equipos/{team}/agentes', [TeamAgentsController::class, 'index'])->name('teams.agents');
    Route::get('equipos/{token}/unirse', [TeamMembershipController::class, 'index'])->name('membership.index');
    Route::post('equipos/{token}/unirse', [TeamMembershipController::class, 'store'])->name('membership.store');
    Route::get('equipos-eliminar/{data}', [TeamsController::class, 'delete'])->name('teams.delete');

    // Módulos de Configuración Global (Supervisores)
    Route::resource('usuarios', UsersController::class)->only(['index', 'destroy', 'create'])->names('users');
    Route::post('usuarios/guardar', [UsersController::class, 'store'])->name('user.store');
    Route::get('usuarios/{user}/suplantar', [UsersController::class, 'impersonate'])->name('users.impersonate');
    Route::resource('configuracion', SettingsController::class)->only(['edit', 'update'])->names('settings');
    Route::get('tipos-de-ticket', [TicketTypesController::class, 'index'])->name('ticketTypes.index');

    // Reportes y Estadísticas
    Route::get('reportes', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('estadisticas', [ReportsController::class, 'analytics'])->name('reports.analytics');
});

/*
|--------------------------------------------------------------------------
| Zona de Enlaces Públicos (Tokens para responder desde correo)
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'cliente'], function () {
    Route::get('tickets/{token}', [RequesterTicketsController::class, 'show'])->name('requester.tickets.show');
    Route::post('tickets/{token}/comentarios', [RequesterCommentsController::class, 'store'])->name('requester.comments.store');
    Route::get('tickets/{token}/calificar', [RequesterTicketsController::class, 'rate'])->name('requester.tickets.rate');
});
