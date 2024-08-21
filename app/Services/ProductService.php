<?php

namespace App\Services;

use App\Models\Product;
use App\DTO\ProductUpdateDTO;
use App\DTO\ImageDTO;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function updateProduct(Product $product, ProductUpdateDTO $dto)
    {
        DB::beginTransaction();

        try {
            $product->update($dto->product);

            if (isset($dto->images['deleted']) && count($dto->images['deleted']) > 0) {
                $this->deleteImages($product, $dto->images['deleted']);
            }

            if (isset($dto->images['created']) && count($dto->images['created']) > 0) {
                $this->createImages($product, $dto->images['created']);
            }

            DB::commit();

            return $product->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function deleteImages(Product $product, array $imageIds)
    {
        foreach ($imageIds as $attachmentId) {
            $attachment = $product->attachments()->find($attachmentId);
            if ($attachment) {
                $attachment->delete();
            }
        }
    }

    private function createImages(Product $product, array $images)
    {
        foreach ($images as $imageData) {
            $imageDTO = ImageDTO::fromArray($imageData);
            $product->attachments()->create($imageDTO->toArray());
        }
    }
}
