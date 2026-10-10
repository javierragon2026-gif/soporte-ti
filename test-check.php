<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'like', 'aux.logistica%')->first();
$t = \Illuminate\Support\Facades\Http::asForm()->post('https://login.microsoftonline.com/'.env('M365_TENANT_ID').'/oauth2/v2.0/token', [
    'client_id'=>env('M365_CLIENT_ID'),
    'client_secret'=>env('M365_CLIENT_SECRET'),
    'scope'=>'https://graph.microsoft.com/.default',
    'grant_type'=>'client_credentials'
])->json('access_token');

$res = \Illuminate\Support\Facades\Http::withToken($t)
    ->get("https://graph.microsoft.com/v1.0/users/{$user->azure_id}");

print_r(json_decode($res->body(), true));
