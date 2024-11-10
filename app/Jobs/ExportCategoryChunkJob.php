<?php

namespace App\Jobs;

use App\Exports\CategoryExportWithChunks;
use App\Exports\CategoryExportWithChunksQuery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Category;
use App\Jobs\SendExportNotification;


class ExportCategoryChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filters;
    protected $batchSize;
    protected $user;


    public function __construct($filters, $batchSize = 7000, $user)
    {
        $this->filters = $filters;
        $this->batchSize = $batchSize;
        $this->user = $user;
    }

    public function handle()
    {
        $totalRows = Category::when(isset($this->filters['title']), function ($query) {
            $query->where('title', 'like', '%' . $this->filters['title'] . '%');
        })->count();

        $totalBatches = (int) ceil($totalRows / $this->batchSize);
        $fileNames = [];

        for ($i = 0; $i < $totalBatches; $i++) {
            $fileName = "category_" . uniqid() . "_part_{$i}.xlsx";
            // $filePath = "public/{$fileName}";
            $offset = $i * $this->batchSize;

            Excel::store(
                new CategoryExportWithChunks($this->filters, $offset, $this->batchSize),
                $fileName,
                'public'
            );

            $fileNames[] = $fileName;
            \Log::info("Export completed for batch {$i} with offset {$offset} and batch size {$this->batchSize}");
        }

        SendExportNotification::dispatch($this->user, $fileNames);
    }

    public function uniqueId()
    {

        return "{$this->filePath}-{$this->offset}";
    }

}
