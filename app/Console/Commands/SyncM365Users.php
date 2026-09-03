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

        $tokenResponse = Http::asForm()->post('https://login.microsoftonline.com/' . env('AZURE_TENANT_ID') . '/oauth2/v2.0/token', [
            'client_id'     => env('AZURE_CLIENT_ID'),
            'client_secret' => env('AZURE_CLIENT_SECRET'),
            'scope'         => 'https://graph.microsoft.com/.default',
            'grant_type'    => 'client_credentials',
        ]);

        if ($tokenResponse->failed()) return $this->error('Fallo al obtener token de Azure.');

        $graphResponse = Http::withToken($tokenResponse->json('access_token'))
            ->get('https://graph.microsoft.com/v1.0/users?$select=displayName,mail,userPrincipalName,accountEnabled&$top=999');

        $dominios = ['ragon.com.mx', 'bise.com.mx'];
        $sistemas = [
            'jeduardo@ragon.com.mx',
            'jramirez@ragon.com.mx',
            'analista.datos@ragon.com.mx',
            'ricardo.mancilla@ragon.com.mx',
            'rguzman@ragon.com.mx',
        ];

        foreach ($graphResponse->json('value') as $apiUser) {
            if (!$apiUser['accountEnabled']) continue;

            $email = strtolower($apiUser['mail'] ?? $apiUser['userPrincipalName']);
            if (!in_array(substr(strrchr($email, "@"), 1), $dominios)) continue;

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name'     => $apiUser['displayName'] ?? explode('@', $email)[0],
                    'password' => bcrypt(Str::random(16)),
                    'admin'    => in_array($email, $sistemas) ? 1 : 0,
                ]
            );
        }

        $this->info('Sincronización finalizada con éxito.');
    }
}
