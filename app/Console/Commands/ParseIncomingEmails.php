<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ParseIncomingEmails extends Command
{
    protected $signature = 'tickets:parse-emails';
    protected $description = 'Revisa la bandeja de entrada de M365 (Graph API) y convierte los correos en tickets';

    public function handle()
    {
        $this->info('Conectando a Microsoft Graph API...');

        $tenantId = env('M365_TENANT_ID');
        $clientId = env('M365_CLIENT_ID');
        $clientSecret = env('M365_CLIENT_SECRET');
        $mailbox = env('EMAIL_FROM', 'sistemas@ragon.com.mx');

        if (!$tenantId || !$clientId || !$clientSecret) {
            $this->error('Credenciales de M365 no configuradas en el .env');
            return;
        }

        try {
            // 1. Obtener Token de Acceso
            $tokenResponse = Http::asForm()->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'scope' => 'https://graph.microsoft.com/.default',
                'grant_type' => 'client_credentials',
            ]);

            if (!$tokenResponse->successful()) {
                throw new \Exception('No se pudo obtener el token de Microsoft Graph: ' . $tokenResponse->body());
            }
            $token = $tokenResponse->json('access_token');

            // 2. Buscar correos no leídos
            $messagesResponse = Http::withToken($token)
                ->get("https://graph.microsoft.com/v1.0/users/{$mailbox}/messages?\$filter=isRead eq false&\$top=15");

            if (!$messagesResponse->successful()) {
                throw new \Exception('Error leyendo buzón: ' . $messagesResponse->body());
            }

            $messages = $messagesResponse->json('value') ?? [];
            $count = 0;

            foreach ($messages as $message) {
                $senderEmail = $message['from']['emailAddress']['address'] ?? null;
                $subject = $message['subject'] ?? 'Sin Asunto';
                $body = $message['body']['content'] ?? 'Sin descripción';
                $messageId = $message['id'];

                if (!$senderEmail) continue;

                // Extraer a los copiados (TO y CC)
                $emailTo = [];
                if (!empty($message['toRecipients'])) {
                    foreach ($message['toRecipients'] as $rec) {
                        $emailTo[] = $rec['emailAddress']['address'];
                    }
                }
                
                $emailCc = [];
                if (!empty($message['ccRecipients'])) {
                    foreach ($message['ccRecipients'] as $rec) {
                        $emailCc[] = $rec['emailAddress']['address'];
                    }
                }

                // Filtrar el correo de sistemas para no generar bucles
                $emailTo = array_diff($emailTo, [$mailbox, strtolower($mailbox)]);
                $emailCc = array_diff($emailCc, [$mailbox, strtolower($mailbox)]);

                // Convertir arreglos a string separados por coma (o JSON, pero texto simple sirve)
                $toStr = implode(',', $emailTo);
                $ccStr = implode(',', $emailCc);

                $user = User::where('email', $senderEmail)->first();

                if ($user) {
                    $ticket = Ticket::create([
                        'title' => substr($subject, 0, 200),
                        'body' => $body,
                        'status' => 1,
                        'user_id' => $user->id,
                        'categoria' => 'SOPORTE',
                        'priority' => 2,
                        'email_to' => $toStr,
                        'email_cc' => $ccStr,
                        'message_id' => $messageId,
                    ]);

                    $count++;
                    $this->info("Ticket creado para $senderEmail con CC a [$ccStr]");
                    event(new \App\Events\TicketUpdated($ticket));
                } else {
                    Log::warning("Correo ignorado: Remitente $senderEmail no existe.");
                }

                // Marcar como leído
                Http::withToken($token)
                    ->patch("https://graph.microsoft.com/v1.0/users/{$mailbox}/messages/{$messageId}", [
                        'isRead' => true
                    ]);
            }

            $this->info("¡Proceso finalizado! Se crearon $count tickets.");
            
        } catch (\Exception $e) {
            $this->error('Error Graph API: ' . $e->getMessage());
            Log::error('Graph API Parse Error: ' . $e->getMessage());
        }
    }
}
