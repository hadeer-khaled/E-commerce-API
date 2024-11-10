<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CategoryExportWithChunksQuery implements FromQuery, WithHeadings, WithStyles
{
    use Exportable;

    // protected $filters;

    // public function __construct(array $filters = [])
    // {
    //     $this->filters = $filters;
    // }

    // public function query()
    // {
    //     $query = Category::query();
    //     if (isset($this->filters['title'])) {
    //         $query->where('title', 'like', '%' . $this->filters['title'] . '%');
    //     }

    //     return $query->select('id', 'title');
    // }
    protected $filters;
    protected $offset;
    protected $batchSize;

    public function __construct(array $filters, int $offset = 0, int $batchSize = 7000)
    {
        $this->filters = $filters;
        $this->offset = $offset;
        $this->batchSize = $batchSize;
    }

    public function query()
    {
        $query = Category::query();

        if (isset($this->filters['title'])) {
            $query->where('title', 'like', '%' . $this->filters['title'] . '%');
        }
        \Log::info("FromQueryKOKOSkipTest::: Applying offset: {$this->offset}, limit: {$this->batchSize}");


        return $query->select('id', 'title')
                     ->skip($this->offset)
                     ->take($this->batchSize);
        // return $query->select('id', 'title')
        // ->customPaginate($this->offset, $this->batchSize);
    }
    // public function chunkSize(): int
    // {
    //     return 250;
    // }

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
