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
            $path = $image->store('images', 'public');

            Attachment::create([
                'filename' => $path,
                'attachable_id' => $model->id,
                'attachable_type' => get_class($model),
            ]);

        }
    }
}
