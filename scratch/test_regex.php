<?php
$lines = [
    "1. Langkah selanjutnya adalah...",
    "A. Menggoreng",
    "B. Merebus",
    "C. Mengukus",
    "Kunci: A"
];

$questions = [];
$currentQuestion = null;
foreach ($lines as $line) {
    $cleanLine = trim(strip_tags($line));
    
    if (preg_match('/^(\d+)\.\s+(.*)/', $cleanLine, $matches)) {
        if ($currentQuestion) $questions[] = $currentQuestion;
        $currentQuestion = ['content' => $cleanLine, 'options' => [], 'answer' => ''];
        echo "Matched Q: $cleanLine\n";
        continue;
    }
    
    if ($currentQuestion && preg_match('/^(?:\[\s*\]\s*)?([A-Ea-e])\.\s+(.*)/i', $cleanLine, $matches)) {
        $currentQuestion['options'][$matches[1]] = $line;
        echo "Matched Opt: $cleanLine\n";
        continue;
    }
    
    if ($currentQuestion && preg_match('/^(?:Kunci|Jawaban):\s*(.*)/i', $cleanLine, $matches)) {
        $currentQuestion['answer'] = $matches[1];
        echo "Matched Ans: $cleanLine\n";
        continue;
    }
}
if ($currentQuestion) $questions[] = $currentQuestion;

print_r($questions);
