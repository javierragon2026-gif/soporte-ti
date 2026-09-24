<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;

class ParseIncomingEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:parse-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revisa la bandeja de entrada de soporte y convierte los correos en tickets de sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Conectando a la bandeja de correo...');

        try {
            // Se asume que el archivo config/imap.php (publicado de Webklex) usará las credenciales del .env
            $client = Client::account('default');
            $client->connect();
            
            // Obtenemos la bandeja de entrada
            $folder = $client->getFolder('INBOX');

            // Buscamos correos no leídos
            $messages = $folder->query()->unseen()->get();
            $count = 0;

            foreach ($messages as $message) {
                // Sacar el correo del remitente original
                $sender = $message->getFrom()[0]->mail;
                $subject = $message->getSubject() ?? 'Sin Asunto';
                // Usar texto plano preferentemente, sino el HTML
                $body = $message->getTextBody() ?? $message->getHTMLBody() ?? 'Sin descripción';

                // Ver si el correo pertenece a algún empleado
                $user = User::where('email', $sender)->first();

                if ($user) {
                    // Creamos el ticket a su nombre
                    $ticket = Ticket::create([
                        'title' => substr($subject, 0, 200), // Límite por base de datos
                        'body' => $body,
                        'status' => 1, // Nuevo
                        'user_id' => $user->id,
                        'categoria' => 'SOPORTE', // Categoria por defecto
                        'priority' => 2, // Normal
                    ]);

                    // Marcamos el correo como leído para no duplicarlo en la siguiente ronda
                    $message->setFlag('Seen');
                    $count++;
                    $this->info("Ticket creado para el usuario $sender");
                    
                    // Disparamos el WebSocket
                    event(new \App\Events\TicketUpdated($ticket));
                } else {
                    // El usuario no existe en la base de datos local
                    // Podríamos ignorarlo, crear un ticket genérico, o guardarlo en log
                    Log::warning("Correo de soporte ignorado: Remitente $sender no existe en la DB local.");
                    // Opcionalmente marcarlo como leído de todos modos para que no estanque el sistema
                    $message->setFlag('Seen');
                }
            }

            $this->info("¡Proceso finalizado! Se crearon $count tickets desde el correo electrónico.");
            
        } catch (\Exception $e) {
            $this->error('Error al procesar correos IMAP: ' . $e->getMessage());
            Log::error('IMAP Parse Error: ' . $e->getMessage());
        }
    }
}
