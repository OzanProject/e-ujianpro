<?php

use App\Models\Student;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting email cleanup...\n";

// 1. Find emails that are duplicated across the entire table (Global Duplicates)
// We want to handle them carefully.
// Actually, we only care about duplicates that would violate the NEW constraint:
// i.e., "Same Email AND Same Created_By".
// AND duplicates that violate the OLD constraint (if we want to restore sanity): "Same Email Global".

// Let's just fix ALL global duplicates by appending _duplicate_{id} to all but the latest one.
// This ensures they are unique globally AND locally.

$duplicates = DB::table('students')
    ->select('email', DB::raw('count(*) as total'))
    ->whereNotNull('email')
    ->groupBy('email')
    ->having('total', '>', 1)
    ->get();

$fixedCount = 0;

foreach ($duplicates as $dup) {
    echo "Processing duplicate email: " . $dup->email . "\n";
    
    // Get all students with this email, ordered by ID desc
    $students = Student::where('email', $dup->email)->orderBy('id', 'desc')->get();
    
    // Keep the first (latest), rename the rest
    $first = true;
    foreach ($students as $student) {
        if ($first) {
            $first = false;
            continue;
        }
        
        $newEmail = $student->email . '_dup_' . $student->id;
        // Truncate if too long (email max 255)
        if (strlen($newEmail) > 255) {
            $newEmail = substr($student->email, 0, 200) . '_dup_' . $student->id;
        }
        
        echo "  - Renaming Student ID {$student->id} to $newEmail\n";
        $student->update(['email' => $newEmail]);
        $fixedCount++;
    }
}

echo "Cleanup complete. Fixed $fixedCount duplicate emails.\n";
