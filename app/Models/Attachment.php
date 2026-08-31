<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Attachment extends Model
{
    use HasFactory;

    protected $table = 'attachments';

    protected $guarded = [];

    /**
     * Modelo al que pertenece el archivo.
     *
     * Puede ser Comment, Note, etc.
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * Guarda un archivo adjunto.
     */
    public static function storeAttachmentFromRequest(
        Request $request,
        Model $model
    ) {
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');

            $path = $file->store('attachments', 'public');

            return $model->attachments()->create([
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'user_id' => auth()->id(),
            ]);
        }

        return null;
    }
}
