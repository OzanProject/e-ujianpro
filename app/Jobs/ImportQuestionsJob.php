<?php

namespace App\Jobs;

use App\Imports\QuestionsImport;
use App\Services\WordQuestionImporter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportQuestionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $subjectId;
    protected $extension;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, int $subjectId, string $extension, int $userId)
    {
        $this->filePath = $filePath;
        $this->subjectId = $subjectId;
        $this->extension = $extension;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fullPath = Storage::path($this->filePath);

        try {
            if ($this->extension === 'docx') {
                $importer = new WordQuestionImporter($this->subjectId);
                $result = $importer->import($fullPath);
                
                if (!empty($result['errors'])) {
                    Log::warning("Word Import Errors for User {$this->userId}: " . json_encode($result['errors']));
                }
            } else {
                Excel::import(new QuestionsImport($this->subjectId), $fullPath);
            }
        } catch (\Throwable $e) {
            Log::error("Import failed for User {$this->userId}: " . $e->getMessage());
        } finally {
            // Bersihkan file sementara setelah diproses (sukses/gagal)
            if (Storage::exists($this->filePath)) {
                Storage::delete($this->filePath);
            }
        }
    }
}
