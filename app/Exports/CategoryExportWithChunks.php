<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\Exportable;

class CategoryExportWithChunks implements FromQuery, WithHeadings, WithCustomChunkSize
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Category::query();
        if (isset($this->filters['title'])) {
            $query->where('title', 'like', '%' . $this->filters['title'] . '%');
        }

        return $query->select('id', 'title');
    }

    public function chunkSize(): int
    {
        return 250;  
    }

    public function headings(): array
    {
        return [
            'ID',           
            'Title',
        ];
    }

}
