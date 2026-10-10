<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class GraphMailHelper
{
    public static function send($to, $subject, $htmlBody)
    {
        if (!env('EMAIL_ENABLED', true)) return;

        $tenantId = env('M365_TENANT_ID');
        $clientId = env('M365_CLIENT_ID');
        $clientSecret = env('M365_CLIENT_SECRET');
        $mailbox = env('EMAIL_FROM', 'sistemas@ragon.com.mx');

        if ($tenantId && $clientId && $clientSecret) {
            try {
                $tokenResponse = Http::asForm()->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'scope' => 'https://graph.microsoft.com/.default',
                    'grant_type' => 'client_credentials',
                ]);
                
                if ($tokenResponse->successful()) {
                    $token = $tokenResponse->json('access_token');
                    $toRecipients = [['emailAddress' => ['address' => $to]]];
                    
                    $mailPayload = [
                        'message' => [
                            'subject' => $subject,
                            'body' => [
                                'contentType' => 'HTML',
                                'content' => $htmlBody
                            ],
                            'toRecipients' => $toRecipients,
                        ],
                        'saveToSentItems' => 'false'
                    ];
                    
                    Http::withToken($token)
                        ->post("https://graph.microsoft.com/v1.0/users/{$mailbox}/sendMail", $mailPayload);
                }
            } catch (\Exception $e) {
                Log::error('Graph API Error: ' . $e->getMessage());
            }
        } else {
            // Placeholder fallback si usan SMTP normal
            Log::info("Enviando correo (sin Graph) a $to: $subject");
        }
    }
}
