<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$t = \Illuminate\Support\Facades\Http::asForm()->post('https://login.microsoftonline.com/'.env('M365_TENANT_ID').'/oauth2/v2.0/token', [
    'client_id'=>env('M365_CLIENT_ID'),
    'client_secret'=>env('M365_CLIENT_SECRET'),
    'scope'=>'https://graph.microsoft.com/.default',
    'grant_type'=>'client_credentials'
])->json('access_token');

list($header, $payload, $sig) = explode('.', $t);
echo base64_decode($payload);
