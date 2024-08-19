<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductResource;

class CategoryResource extends JsonResource
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
            'image' => $this->attachment ?  asset('storage/' . $this->attachment->filename) : null  ,
            'products'=>ProductResource::collection($this->products)
            
        ];
    }
}
