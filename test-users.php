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

$res = \Illuminate\Support\Facades\Http::withToken($t)
    ->get('https://graph.microsoft.com/v1.0/users?$select=id,displayName,mail,userPrincipalName,accountEnabled&$top=999');

echo $res->body();
