<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\RequesterTicketsController;
use App\Http\Controllers\RequesterCommentsController;

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

    Route::get('mis-tickets', [RequesterTicketsController::class, 'index'])->name('cliente.tickets.index');

    Route::group(['prefix' => 'cliente'], function () {
        Route::get('tickets/crear', [TicketsController::class, 'crearCliente'])->name('cliente.tickets.crear');
        Route::get('tickets/nuevo', [TicketsController::class, 'formularioCliente'])->name('cliente.tickets.formulario');

        Route::get('tickets/{ticket}', [RequesterTicketsController::class, 'show'])->name('cliente.tickets.show');
        Route::post('tickets/{ticket}/comentarios', [RequesterCommentsController::class, 'store'])->name('requester.comments.store');
        Route::get('tickets/{ticket}/calificar', [RequesterTicketsController::class, 'rate'])->name('requester.tickets.rate');
        
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

    Route::get('tickets/fusionar', [TicketsMergeController::class, 'index'])->name('tickets.merge.index');
    Route::get('tickets/buscar/{text}', [TicketsSearchController::class, 'index'])->name('tickets.search');
    
    // Le quitamos 'store' al resource porque ya lo declaramos arriba para todos
    Route::resource('tickets', TicketsController::class)->except(['store', 'edit', 'destroy']);

    Route::post('tickets/{ticket}/asignar', [TicketsAssignController::class, 'store'])->name('tickets.assign');
    Route::post('tickets/{ticket}/comentarios', [CommentsController::class, 'store'])->name('tickets.comments.store');
    Route::post('tickets/{ticket}/etiquetas', [TicketsTagsController::class, 'store'])->name('tickets.tags.store');
    Route::delete('tickets/{ticket}/etiquetas/{tag}', [TicketsTagsController::class, 'destroy'])->name('tickets.tags.destroy');
    Route::post('tickets/{ticket}/reabrir', [TicketsController::class, 'reopen'])->name('tickets.reopen');
    Route::post('tickets/{ticket}/escalar', [TicketsEscalateController::class, 'store'])->name('tickets.escalate.store');
    Route::delete('tickets/{ticket}/escalar', [TicketsEscalateController::class, 'destroy'])->name('tickets.escalate.destroy');

    Route::get('clientes', [RequestersController::class, 'index'])->name('requesters.index');
    Route::get('adjuntos/{filename}', [AttachmentsController::class, 'show'])->name('attachments');
    Route::resource('tareas', TasksController::class)->only(['index', 'update', 'destroy']);

    Route::resource('equipos', TeamsController::class)->names('teams');
    Route::get('equipos/{team}/agentes', [TeamAgentsController::class, 'index'])->name('teams.agents');
    Route::get('equipos/{token}/unirse', [TeamMembershipController::class, 'index'])->name('membership.index');
    Route::post('equipos/{token}/unirse', [TeamMembershipController::class, 'store'])->name('membership.store');
    Route::get('equipos-eliminar/{data}', [TeamsController::class, 'delete'])->name('teams.delete');

    Route::resource('usuarios', UsersController::class)->only(['index', 'destroy', 'create'])->names('users');
    Route::post('usuarios/guardar', [UsersController::class, 'store'])->name('user.store');
    Route::get('usuarios/{user}/suplantar', [UsersController::class, 'impersonate'])->name('users.impersonate');
    Route::resource('configuracion', SettingsController::class)->only(['edit', 'update'])->names('settings');
    Route::get('tipos-de-ticket', [TicketTypesController::class, 'index'])->name('ticketTypes.index');

    Route::get('reportes', [ReportsController::class, 'index'])->name('reports.index');
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