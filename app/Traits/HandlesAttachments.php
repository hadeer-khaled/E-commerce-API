<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Models\Attachment;

trait HandlesAttachments
{
    /**
     * Handle the attachment of an image to a model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $fileInputName
     * @return void
     */
    public function handleAttachment($model, Request $request, $fileInputName = 'image' , $update = false)
    {
        if ($request->hasFile($fileInputName)) {
            if ($update){
                $model->attachment()->delete();
            }

            $image = $request->file($fileInputName);
            $originalName = $image->getClientOriginalName();
            $storageName = $image->hashName();
            $path = $image->store('images', 'public');
            $url = asset('storage/' . $path);

            Attachment::create([
                'original_filename' => $originalName,
                'storage_filename' => $storageName,
                'url' => $url,
                'attachable_id' => $model->id,
                'attachable_type' => get_class($model),
            ]);

        }
    }
}
