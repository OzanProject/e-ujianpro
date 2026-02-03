<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$indices = DB::select('SHOW INDEX FROM students');

echo "Raw Indices Data:\n";
foreach ($indices as $idx) {
    echo "Key: " . $idx->Key_name . " | Column: " . $idx->Column_name . " | Unique: " . ($idx->Non_unique == 0 ? 'Yes' : 'No') . "\n";
}
