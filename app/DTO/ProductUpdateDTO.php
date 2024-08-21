<?php

namespace App\DTO;

class ProductUpdateDTO
{
    public $product;
    public $images;

    public function __construct(array $product, array $images)
    {
        $this->product = $product;
        $this->images = $images;
    }

    public static function fromArray(array $data)
    {
        return new self(
            $data['product'],
            $data['images']
        );
    }
}
