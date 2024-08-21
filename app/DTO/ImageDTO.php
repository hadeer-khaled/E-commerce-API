<?php

namespace App\DTO;

class ImageDTO
{
    public $originalFilename;
    public $storageFilename;
    public $url;

    public function __construct(string $originalFilename, string $storageFilename, string $url)
    {
        $this->originalFilename = $originalFilename;
        $this->storageFilename = $storageFilename;
        $this->url = $url;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['original_filename'],
            $data['storage_filename'],
            $data['url']
        );
    }

    public function toArray(): array
    {
        return [
            'original_filename' => $this->originalFilename,
            'storage_filename' => $this->storageFilename,
            'url' => $this->url,
        ];
    }
}
