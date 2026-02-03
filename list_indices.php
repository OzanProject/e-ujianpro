<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$indices = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes('students');

echo "Indices on 'students' table:\n";
foreach ($indices as $index) {
    echo "- " . $index->getName() . " (Columns: " . implode(', ', $index->getColumns()) . ")\n";
}
