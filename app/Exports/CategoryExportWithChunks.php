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

class CategoryExportWithChunks implements FromQuery, WithHeadings, WithCustomChunkSize, WithStyles
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
            'الرقم',           
            'الاسم',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);
    
        // $sheet->getStyle('A1:B1')->applyFromArray([
        //     'font' => [
        //         'bold' => true,
        //     ],
        //     'fill' => [
        //         'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        //         'startColor' => [
        //             'argb' => 'FFFF0000', // Red background color (ARGB format)
        //         ],
        //     ],
        // ]);
    
        return [  
            1    => ['font' => ['bold' => true]],
        ];
    }
    
    

    // public function registerEvents(): array
    // {
    //     return [
    //         AfterSheet::class => function(AfterSheet $event) {
    //             // Set the direction of the worksheet to RTL
    //             $event->sheet->getDelegate()->setRightToLeft(true);
    
    //             // Test if the event is being triggered by setting a background color
    //             $event->sheet->getDelegate()->getStyle('A1:B1')->applyFromArray([
    //                 'fill' => [
    //                     'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
    //                     'color' => ['argb' => 'FFFF0000'],
    //                 ],
    //             ]);
    //         },
    //     ];
    // }
    
}
