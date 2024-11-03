<?php

namespace App\Jobs;

use App\Exports\CategoryExportWithChunks;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ExportCategoryChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filters;
    protected $offset;
    protected $batchSize;
    protected $filePath;

    public function __construct($filters, $offset, $batchSize, $filePath)
    {
        $this->filters = $filters;
        $this->offset = $offset;
        $this->batchSize = $batchSize;
        $this->filePath = $filePath;
    }

    public function handle()
    {
        \Log::info("Handling job with unique ID: " . $this->uniqueId());

        Excel::store(
            new CategoryExportWithChunks($this->filters, $this->offset, $this->batchSize),
            $this->filePath,
            'public'
        );


    }

    public function uniqueId()
    {

        return "{$this->filePath}-{$this->offset}";
    }

}
