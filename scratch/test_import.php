<?php
require 'vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\WordQuestionImporter;

$phpWord = new \PhpOffice\PhpWord\PhpWord();
$section = $phpWord->addSection();

$section->addText('Contoh Pilihan Ganda Tunggal:', ['bold' => true]);
$section->addText('Budi ingin membuat nasi goreng, ia menyiapkan wajan.');
$section->addText('1. Langkah selanjutnya adalah...');
$section->addText('A. Menggoreng');
$section->addText('B. Merebus');
$section->addText('C. Mengukus');
$section->addText('Kunci: A');
$section->addText('Tingkat: Mudah');
$section->addText('');

$tempFile = storage_path('app/temp_test.docx');
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save($tempFile);

$importer = new WordQuestionImporter(1);
$result = $importer->import($tempFile);

print_r($result);
