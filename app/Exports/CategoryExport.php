<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoryExport implements FromCollection , WithHeadings
{
    protected $filters;

    // Accept filters through the constructor
    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
   public function collection()
    {
        $query = Category::query();

        if (isset($this->filters['title'])) {
            $query->where('title', 'like', '%' . $this->filters['title'] . '%');
        }

        return $query->select('id', 'title')->get();

    }
    public function headings(): array
    {
        return [
            'ID',           
            'Title', 
        ];
    }
}
