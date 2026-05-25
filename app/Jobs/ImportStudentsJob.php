<?php

namespace App\Jobs;

use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\ExamRoom;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ImportStudentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 menit per job
    public $tries = 3;

    protected $rows;
    protected $studentGroupId;
    protected $userId;
    protected $maxStudents;

    public $importedCount = 0;
    public $skippedCount = 0;
    public $duplicates = [];

    /**
     * Create a new job instance.
     */
    public function __construct(array $rows, $studentGroupId, $userId, $maxStudents)
    {
        $this->rows = $rows;
        $this->studentGroupId = $studentGroupId;
        $this->userId = $userId;
        $this->maxStudents = $maxStudents;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $currentCount = Student::where('created_by', $this->userId)->count();

        foreach ($this->rows as $row) {
            // Skip if Name or NIS is empty
            if (empty($row['nama_lengkap']) || empty($row['nis'])) {
                continue;
            }

            $nis = trim($row['nis']);
            $password_text = !empty($row['password_opsional']) ? $row['password_opsional'] : $nis;

            try {
                // 1. Find or Create User
                $user = User::where('email', $nis)->first();

                if (!$user) {
                    $user = User::create([
                        'name' => $row['nama_lengkap'],
                        'email' => $nis,
                        'password' => Hash::make($password_text),
                        'role' => 'peserta_ujian',
                        'created_by' => $this->userId,
                    ]);
                }

                // 2. Find or Create Student
                $student = Student::where('nis', $nis)
                    ->where('created_by', $this->userId)
                    ->first();

                if ($student) {
                    // Existing student - Track as Duplicate
                    $this->duplicates[] = "{$row['nama_lengkap']} ($nis)";
                } else {
                    // Check Quota before creating
                    if (!is_null($this->maxStudents) && $currentCount >= $this->maxStudents) {
                        $this->skippedCount++;
                        continue;
                    }

                    $student = new Student();
                    $student->nis = $nis;
                    $student->user_id = $user->id;
                    $student->created_by = $this->userId;

                    $currentCount++;
                    $this->importedCount++;
                }

                // 3. Update Attributes
                $student->name = $row['nama_lengkap'];
                $student->gender = isset($row['jenis_kelamin_lp']) ? strtoupper(trim($row['jenis_kelamin_lp'])) : $student->gender;
                $student->nisn = isset($row['nisn_opsional']) ? trim($row['nisn_opsional']) : $student->nisn;
                $student->email = $user->email;
                $student->password = $user->password;
                $student->password_text = $student->password_text ?: $password_text;
                $student->kelas = $row['kelas'] ?? $student->kelas;
                $student->jurusan = $row['jurusan'] ?? $student->jurusan;

                // Handle Student Group
                if ($this->studentGroupId) {
                    $student->student_group_id = $this->studentGroupId;
                } elseif (!empty($row['kelompok_opsional'])) {
                    $groupName = trim($row['kelompok_opsional']);
                    $group = StudentGroup::firstOrCreate(
                        ['name' => $groupName, 'created_by' => $this->userId],
                        ['created_by' => $this->userId]
                    );
                    $student->student_group_id = $group->id;
                }

                // Handle Exam Room
                if (!empty($row['ruangan_opsional'])) {
                    $roomName = trim($row['ruangan_opsional']);
                    $room = ExamRoom::where('name', $roomName)
                        ->where('created_by', $this->userId)
                        ->first();
                    if ($room) {
                        $student->exam_room_id = $room->id;
                    }
                }

                $student->save();

            } catch (\Exception $e) {
                Log::error("Import student failed for NIS {$nis}: " . $e->getMessage());
                $this->skippedCount++;
            }
        }

        Log::info("ImportStudentsJob completed: {$this->importedCount} imported, {$this->skippedCount} skipped, " . count($this->duplicates) . " duplicates");
    }
}
