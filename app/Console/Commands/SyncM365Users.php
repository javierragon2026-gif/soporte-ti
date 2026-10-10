<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Str;

class SyncM365Users extends Command
{
    protected $signature = 'sync:m365-users';
    protected $description = 'Descarga la libreta de Microsoft 365 corporativa';

    public function handle()
    {
        $this->info('Solicitando acceso a Microsoft Entra ID...');

        $tokenResponse = Http::asForm()->post('https://login.microsoftonline.com/' . env('M365_TENANT_ID') . '/oauth2/v2.0/token', [
            'client_id'     => env('M365_CLIENT_ID'),
            'client_secret' => env('M365_CLIENT_SECRET'),
            'scope'         => 'https://graph.microsoft.com/.default',
            'grant_type'    => 'client_credentials',
        ]);

        if ($tokenResponse->failed()) return $this->error('Fallo al obtener token de Azure.');

        $graphResponse = Http::withToken($tokenResponse->json('access_token'))
            ->get('https://graph.microsoft.com/v1.0/users?$select=id,displayName,mail,userPrincipalName,jobTitle,department,accountEnabled&$top=999');

        if ($graphResponse->failed()) {
            \Illuminate\Support\Facades\Log::error('M365 Sync Error: ' . $graphResponse->body());
            return $this->error('Fallo al consultar los usuarios de M365. Verifica los permisos de la API en Azure.');
        }

        $dominios = ['ragon.com.mx', 'bise.mx'];
        $sistemas = [
            'jeduardo@ragon.com.mx',
            'jramirez@ragon.com.mx',
            'analista.datos@ragon.com.mx',
            'ricardo.mancilla@ragon.com.mx',
            'rguzman@ragon.com.mx',
        ];

        $usersList = $graphResponse->json('value') ?? [];

        foreach ($usersList as $apiUser) {
            if (!$apiUser['accountEnabled']) continue;

            $email = strtolower($apiUser['mail'] ?? $apiUser['userPrincipalName']);
            if (!in_array(substr(strrchr($email, "@"), 1), $dominios)) continue;

            $user = User::firstOrNew(['email' => $email]);
            $user->name = $apiUser['displayName'] ?? explode('@', $email)[0];
            $user->azure_id = $apiUser['id'];
            $user->admin = in_array($email, $sistemas) ? 1 : 0;
            
            // Solo generar la contraseña por defecto (12345) si es un usuario NUEVO
            if (!$user->exists) {
                $user->password = bcrypt('12345');
            }
            
            // Laravel optimiza esto y solo hace UPDATE si los campos name/admin cambiaron.
            $user->save();
        }

        // Guardar la marca de tiempo de la última sincronización
        \Illuminate\Support\Facades\Cache::put('last_m365_sync', now());

        $this->info('Sincronización finalizada con éxito.');
    }
}
