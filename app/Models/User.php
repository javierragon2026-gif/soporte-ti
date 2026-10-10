<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'azure_id',
        'job_title',
        'department',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Verifica si el usuario pertenece al departamento de TI (Administrador)
     */
    public function isAdmin()
    {
        $adminEmails = [
            'jeduardo@ragon.com.mx',
            'jramirez@ragon.com.mx',
            'analista.datos@ragon.com.mx',
            'ricardo.mancilla@ragon.com.mx',
            'rguzman@ragon.com.mx'
        ];

        return in_array($this->email, $adminEmails);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'agent_id');
    }

    /**
     * Send the password reset notification using Microsoft Graph API (since SMTP is disabled/not used).
     */
    public function sendPasswordResetNotification($token)
    {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ], false));

        $htmlBody = "<h2>Recuperación de Contraseña</h2>
                     <p>Has solicitado restablecer tu contraseña en el portal de Tickets.</p>
                     <p>Haz clic en el siguiente enlace para crear una nueva contraseña:</p>
                     <p><a href='{$url}' style='display:inline-block; padding:10px 20px; background-color:#1e3c72; color:#ffffff; text-decoration:none; border-radius:5px;'>Restablecer Contraseña</a></p>
                     <p>Si no solicitaste este cambio, puedes ignorar este correo.</p>";

        $tenantId = env('M365_TENANT_ID');
        $clientId = env('M365_CLIENT_ID');
        $clientSecret = env('M365_CLIENT_SECRET');
        $mailbox = env('EMAIL_FROM', 'sistemas@ragon.com.mx');

        if ($tenantId && $clientId && $clientSecret) {
            $tokenResponse = \Illuminate\Support\Facades\Http::asForm()->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'scope' => 'https://graph.microsoft.com/.default',
                'grant_type' => 'client_credentials',
            ]);

            if ($tokenResponse->successful()) {
                $accessToken = $tokenResponse->json('access_token');
                $mailPayload = [
                    'message' => [
                        'subject' => "Restablecer tu contraseña de Tickets",
                        'body' => [
                            'contentType' => 'HTML',
                            'content' => $htmlBody
                        ],
                        'toRecipients' => [
                            ['emailAddress' => ['address' => $this->email]]
                        ],
                    ],
                    'saveToSentItems' => 'false'
                ];
                
                \Illuminate\Support\Facades\Http::withToken($accessToken)
                    ->post("https://graph.microsoft.com/v1.0/users/{$mailbox}/sendMail", $mailPayload);
            }
        }
    }

    protected static function booted()
    {
        static::updated(function ($user) {
            // Solo sincronizamos si cambió algún campo clave y tenemos el ID de Azure
            if ($user->azure_id && $user->wasChanged(['name'])) {
                
                $tenantId = env('M365_TENANT_ID');
                $clientId = env('M365_CLIENT_ID');
                $clientSecret = env('M365_CLIENT_SECRET');
                
                if ($tenantId && $clientId && $clientSecret) {
                    \Illuminate\Support\Facades\Http::macro('graphAuth', function () use ($tenantId, $clientId, $clientSecret) {
                        return \Illuminate\Support\Facades\Http::asForm()->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
                            'client_id' => $clientId,
                            'client_secret' => $clientSecret,
                            'scope' => 'https://graph.microsoft.com/.default',
                            'grant_type' => 'client_credentials',
                        ]);
                    });

                    // Lo ideal sería un Job en Queue, pero por ahora lo disparamos directo
                    try {
                        $tokenResponse = \Illuminate\Support\Facades\Http::graphAuth();
                        if ($tokenResponse->successful()) {
                            $token = $tokenResponse->json('access_token');
                            $payload = [];
                            if ($user->wasChanged('name')) {
                                $payload['displayName'] = $user->name;
                                
                                // Intentar separar el nombre en "Nombre" y "Apellidos"
                                $partes = explode(' ', trim($user->name));
                                if (count($partes) >= 2) {
                                    $payload['givenName'] = array_shift($partes);
                                    $payload['surname'] = implode(' ', $partes);
                                } else {
                                    $payload['givenName'] = $user->name;
                                    $payload['surname'] = "";
                                }
                            }

                            $res = \Illuminate\Support\Facades\Http::withToken($token)
                                ->patch("https://graph.microsoft.com/v1.0/users/{$user->azure_id}", $payload);
                            
                            if ($res->failed()) {
                                \Illuminate\Support\Facades\Log::error('Graph API PATCH Failed: ' . $res->body());
                            } else {
                                \Illuminate\Support\Facades\Log::info('Graph API PATCH Success for ' . $user->email);
                            }
                        } else {
                            \Illuminate\Support\Facades\Log::error('Graph API Auth Failed: ' . $tokenResponse->body());
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Error sync M365 User: ' . $e->getMessage());
                    }
                }
            }
        });
    }
}
