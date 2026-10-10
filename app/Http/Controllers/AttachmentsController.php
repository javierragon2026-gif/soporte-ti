<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Storage;

class AttachmentsController extends Controller
{
    public function show($filename)
    {
        return Storage::download("attachments/{$filename}");
    }
}
