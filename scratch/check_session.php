<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\ExamSession;
$s = ExamSession::find(1);
if ($s) {
    echo "ID: " . $s->id . "\n";
    echo "Is Active: " . ($s->is_active ? 'Yes' : 'No') . "\n";
    echo "Start: " . $s->start_time . "\n";
    echo "End: " . $s->end_time . "\n";
    echo "Duration: " . $s->duration . "\n";
    echo "Now: " . now() . "\n";
} else {
    echo "Session not found";
}
