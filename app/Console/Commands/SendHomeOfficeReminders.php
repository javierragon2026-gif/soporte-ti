<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HomeOfficeRequest;
use App\Helpers\GraphMailHelper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SendHomeOfficeReminders extends Command
{
    protected $signature = 'home-office:send-reminders';
    protected $description = 'Envía recordatorios diarios a los usuarios que deben entregar la laptop de Home Office.';

    public function handle()
    {
        $today = Carbon::today()->toDateString();
        
        $overdueRequests = HomeOfficeRequest::with('user')
            ->where('status', 'active')
            ->whereDate('scheduled_end_date', '<=', $today)
            ->get();

        foreach ($overdueRequests as $request) {
            $user = $request->user;
            if (!$user || !$user->email) continue;
            
            $diasRetraso = Carbon::parse($request->scheduled_end_date)->diffInDays(Carbon::today());
            
            if ($diasRetraso == 0) {
                $titulo = "Recordatorio: Entrega de Laptop de Home Office HOY";
                $mensaje = "Hola {$user->name}, este es un recordatorio para informarte que el día de **HOY ({$request->scheduled_end_date})** termina tu periodo de Home Office y debes presentarte a entregar el equipo en el departamento de Sistemas.";
            } else {
                $titulo = "URGENTE: Equipo de Home Office con Retraso ($diasRetraso días)";
                $mensaje = "Hola {$user->name}, el sistema detecta que tu fecha de entrega programada fue el **{$request->scheduled_end_date}**. Tienes un retraso de **$diasRetraso días** en la entrega del equipo de Home Office. Por favor preséntate inmediatamente en Sistemas para hacer la entrega correspondiente.";
            }

            try {
                $htmlBody = (new \App\Mail\HomeOfficeNotification($request, $titulo, $mensaje))->render();
                GraphMailHelper::send($user->email, $titulo, $htmlBody);
                $this->info("Recordatorio enviado a {$user->email} (HO-{$request->id})");
            } catch (\Exception $e) {
                $this->error("Error enviando recordatorio a {$user->email}: " . $e->getMessage());
                Log::error("Error Home Office Reminder: " . $e->getMessage());
            }
        }

        $this->info("Proceso de recordatorios de Home Office finalizado.");
    }
}
