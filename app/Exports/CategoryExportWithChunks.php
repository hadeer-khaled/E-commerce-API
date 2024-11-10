<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CategoryExportWithChunks implements FromCollection , WithStyles, WithHeadings
{
    use Exportable;

    protected $filters;
    protected $offset;
    protected $batchSize;

    public function __construct(array $filters, int $offset = 0, int $batchSize = 7000)
    {
        $this->filters = $filters;
        $this->offset = $offset;
        $this->batchSize = $batchSize;
    }

    public function collection()
    {
        $query = Category::query();

        if (isset($this->filters['title'])) {
            $query->where('title', 'like', '%' . $this->filters['title'] . '%');
    }

        \Log::info("Collection:::Applying offset: {$this->offset}, limit: {$this->batchSize}");

        return $query->select('id', 'title')
                     ->offset($this->offset)
                     ->limit($this->batchSize)
                     ->get();
    }

    public function headings(): array
    {
        return [
            'الرقم',
            'الاسم',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);

        return [
            1    => ['font' => ['bold' => true]],
        ];
    }




}
