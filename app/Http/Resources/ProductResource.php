<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ImageResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            "id"=>$this->id,
            "title"=>$this->title,
            "description"=>$this->description ?? 'No description available',
            "price"=>$this->price,
            "category_id"=>$this->category->id,
            "category"=>$this->category->title,
            // 'images' => $this->attachments->map(function ($image) {
            //     return asset('storage/' . $image->filename);
            // }),
            "images"=> $this->attachments ?  $this->attachments  : null

        ];
    }
}
