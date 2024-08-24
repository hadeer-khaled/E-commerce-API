<?php

namespace App\DTO;

class ProductUpdateDTO
{
    public $productData;
    public $images;

    public function __construct(array $product, array $images)
    {
        $this->productData = $product;
        $this->images = $images;
    }

    public static function fromArray(array $data)
    {
        return new self(
            $data['productData'],
            $data['images']
        );
    }
}
