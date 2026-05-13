<?php
// Simulation of PhpWord extraction of an auto-numbered document
$rawLines = [
    "[LIST_ITEM_MARKER] Langkah selanjutnya adalah...",
    "[LIST_ITEM_MARKER] Menggoreng",
    "[LIST_ITEM_MARKER] Merebus",
    "[LIST_ITEM_MARKER] Mengukus",
    "Kunci: A",
    "Tingkat: Mudah",
    "[LIST_ITEM_MARKER] Ciri algoritma yang baik adalah...",
    "[LIST_ITEM_MARKER] Jelas",
    "[LIST_ITEM_MARKER] Membingungkan",
    "[LIST_ITEM_MARKER] Cepat",
    "Kunci: A,C",
    "Tingkat: Sedang"
];

$questions = [];
$currentQuestion = null;
$currentReadingText = '';

foreach ($rawLines as $line) {
    $cleanLine = trim(html_entity_decode(strip_tags($line)));

    // Skip BAGIAN headers
    if (preg_match('/^BAGIAN\s/i', $cleanLine)) {
        continue;
    }

    $isExplicitQuestion = preg_match('/^(\d+)\.\s+(.*)/', $cleanLine, $matches);
    $isListItem = str_starts_with($line, '[LIST_ITEM_MARKER] ');
    $isNewListItemQuestion = $isListItem && (!$currentQuestion || !empty($currentQuestion['answer']));

    if ($isExplicitQuestion || $isNewListItemQuestion) {
        if ($currentQuestion) {
            $questions[] = $currentQuestion;
        }
        
        if ($isExplicitQuestion) {
            $htmlWithoutQNumber = preg_replace('/^(\s*<[^>]*>\s*)*\d+\.(\s*<[^>]*>\s*)*\s*/i', '', $line);
        } else {
            $htmlWithoutQNumber = str_replace('[LIST_ITEM_MARKER] ', '', $line);
        }
        
        $currentQuestion = [
            'reading' => $currentReadingText,
            'content' => $htmlWithoutQNumber,
            'options' => [],
            'answer' => '',
            'difficulty' => 'easy',
            'type' => 'multiple_choice'
        ];
        $currentReadingText = ''; 
        continue;
    }

    $isExplicitOption = preg_match('/^(?:\[\s*\]\s*)?([A-Ea-e])[\.\)]\s*(.*)/i', $cleanLine, $matchesOpt);
    
    if ($currentQuestion && empty($currentQuestion['answer']) && ($isExplicitOption || $isListItem)) {
        if ($isListItem) {
            $htmlWithoutPrefix = str_replace('[LIST_ITEM_MARKER] ', '', $line);
            $existingCount = count($currentQuestion['options']);
            $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
            $optKey = $letters[$existingCount] ?? 'A';
        } else {
            $optKey = strtoupper($matchesOpt[1]);
            $htmlWithoutPrefix = preg_replace('/^(\s*<[^>]*>\s*)*(?:\[\s*\]\s*)?[A-Ea-e][\.\)](\s*<[^>]*>\s*)*\s*/i', '', $line);
        }
        
        $currentQuestion['options'][$optKey] = $htmlWithoutPrefix;
        continue;
    }

    if ($currentQuestion && preg_match('/^(?:Kunci|Jawaban):\s*(.*)/i', $cleanLine, $matches)) {
        $answerRaw = strtoupper(trim($matches[1]));
        $answerClean = str_replace('/', ',', $answerRaw);
        $currentQuestion['answer'] = $answerClean;
        
        if (str_contains($answerClean, ',')) {
            if (preg_match('/^[BSbs,\s]+$/', $answerClean)) {
                $currentQuestion['type'] = 'boolean_grid';
            } else {
                $currentQuestion['type'] = 'multiple_choice_complex';
            }
        }
        continue;
    }

    if ($currentQuestion && preg_match('/^(?:Tingkat|Kesulitan|Level):\s*(.*)/i', $cleanLine, $matches)) {
        $diff = strtolower(trim($matches[1]));
        if (str_contains($diff, 'sulit') || str_contains($diff, 'hard')) {
            $currentQuestion['difficulty'] = 'hard';
        } elseif (str_contains($diff, 'sedang') || str_contains($diff, 'medium')) {
            $currentQuestion['difficulty'] = 'medium';
        } else {
            $currentQuestion['difficulty'] = 'easy';
        }
        continue;
    }

    if ($currentQuestion) {
        if (empty($currentQuestion['options'])) {
            $currentQuestion['content'] .= '<br>' . $line;
        } else {
            $lastOptKey = array_key_last($currentQuestion['options']);
            if ($lastOptKey) {
                $currentQuestion['options'][$lastOptKey] .= '<br>' . $line;
            }
        }
    } else {
        if (!empty($currentReadingText)) {
            $currentReadingText .= '<br>';
        }
        $currentReadingText .= $line;
    }
}

if ($currentQuestion) {
    $questions[] = $currentQuestion;
}

print_r($questions);
