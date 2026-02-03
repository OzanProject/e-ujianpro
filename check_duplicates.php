<?php

use App\Models\Student;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$duplicates = DB::table('students')
    ->select('email', 'created_by', DB::raw('count(*) as total'))
    ->whereNotNull('email')
    ->groupBy('email', 'created_by')
    ->having('total', '>', 1)
    ->get();

if ($duplicates->isEmpty()) {
    echo "No duplicates found.\n";
} else {
    echo "Found duplicates:\n";
    foreach ($duplicates as $dup) {
        echo "Email: {$dup->email}, Created By: {$dup->created_by}, Count: {$dup->total}\n";
    }
}
