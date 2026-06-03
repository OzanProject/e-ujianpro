<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Butir Soal - {{ $selectedSession->title }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 20px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-weight-bold { font-weight: bold; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
        }
        .header-table th, .header-table td {
            border: none;
            padding: 2px;
            text-align: left;
        }
        
        .check-icon { font-family: sans-serif; color: #10b981; }
        .cross-icon { font-family: sans-serif; color: #ef4444; }
        
        @media print {
            @page { size: landscape; margin: 1cm; }
            body { padding: 0; }
            .no-print { display: none !important; }
            th, td { border: 1px solid #000 !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 4px;">Cetak Laporan</button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #6c757d; color: white; border: none; cursor: pointer; border-radius: 4px; margin-left: 10px;">Tutup</button>
    </div>

    <div class="text-center" style="margin-bottom: 20px; border-bottom: 3px double #000; padding-bottom: 10px;">
        <table style="width: 100%; border: none; margin: 0;">
            <tr>
                <td style="width: 15%; text-align: left; border: none;">
                    @if($institution && $institution->logo)
                        <img src="{{ asset('storage/' . $institution->logo) }}" style="width: 80px; height: auto;">
                    @endif
                </td>
                <td style="width: 70%; text-align: center; border: none;">
                    <h3 style="margin: 0; font-size: 16px;">{{ $institution->name ?? 'E-UJIAN PRO' }}</h3>
                    <p style="margin: 5px 0 0 0; font-size: 12px;">{{ $institution->address ?? '' }}</p>
                    <h2 style="margin: 15px 0 0 0; font-size: 18px; text-decoration: underline;">ANALISIS BUTIR SOAL</h2>
                </td>
                <td style="width: 15%; border: none;"></td>
            </tr>
        </table>
    </div>

    <table class="header-table" style="width: 100%; margin-bottom: 15px;">
        <tr>
            <td style="width: 15%;">Mata Pelajaran</td>
            <td style="width: 35%;">: <b>{{ $selectedSession->subject->name ?? '-' }}</b></td>
            <td style="width: 15%;">Nama Ujian</td>
            <td style="width: 35%;">: {{ $selectedSession->title }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>: {{ $selectedSession->target_kelas ?? 'Semua Kelas' }}</td>
            <td>Tanggal Ujian</td>
            <td>: {{ \Carbon\Carbon::parse($selectedSession->start_time)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>Total Soal</td>
            <td>: {{ $questions->count() }} Butir</td>
            <td>Nilai KKM</td>
            <td>: <b>{{ $kkm }}</b></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 30px;">No</th>
                <th rowspan="2" style="width: 200px;">Nama Peserta</th>
                <th rowspan="2" style="width: 60px;">Kelas</th>
                <th colspan="{{ $questions->count() }}">Nomor Soal</th>
                <th rowspan="2" style="width: 40px;">Benar</th>
                <th rowspan="2" style="width: 40px;">Salah</th>
                <th rowspan="2" style="width: 50px;">Skor</th>
                <th rowspan="2" style="width: 60px;">Ket.</th>
            </tr>
            <tr>
                @foreach($questions as $index => $question)
                    <th style="width: 20px;">{{ $index + 1 }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                $questionStats = [];
                foreach($questions as $q) {
                    $questionStats[$q->id] = ['correct' => 0, 'total' => 0];
                }
            @endphp

            @forelse($attempts as $attempt)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left">{{ $attempt->student->name }}</td>
                    <td>{{ $attempt->student->group->name ?? '-' }}</td>
                    
                    @php
                        $correctCount = 0;
                        $wrongCount = 0;
                        $attemptAnswers = $attempt->answers->keyBy('question_id');
                    @endphp

                    @foreach($questions as $question)
                        @php
                            $ans = $attemptAnswers->get($question->id);
                            $questionStats[$question->id]['total']++;
                            
                            $isCorrect = false;
                            $score = 0;

                            if ($ans) {
                                if ($question->type == 'essay') {
                                    $score = $ans->score;
                                    $isCorrect = $score > 0;
                                } else {
                                    $isCorrect = $ans->is_correct || ($ans->option && $ans->option->is_correct);
                                }
                            }

                            if ($isCorrect && $question->type != 'essay') {
                                $questionStats[$question->id]['correct']++;
                                $correctCount++;
                            } elseif ($question->type != 'essay') {
                                $wrongCount++;
                            }
                        @endphp
                        
                        <td>
                            @if($question->type == 'essay')
                                {{ $ans ? $score : 0 }}
                            @else
                                @if($isCorrect)
                                    <span class="check-icon">✓</span>
                                @else
                                    <span class="cross-icon">✗</span>
                                @endif
                            @endif
                        </td>
                    @endforeach
                    
                    <td>{{ $correctCount }}</td>
                    <td>{{ $wrongCount }}</td>
                    <td class="font-weight-bold">{{ number_format($attempt->score, 2) }}</td>
                    <td>{{ $attempt->score >= $kkm ? 'Tuntas' : 'Belum' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $questions->count() + 7 }}" style="padding: 20px;">Belum ada data hasil ujian.</td>
                </tr>
            @endforelse
        </tbody>
        @if($attempts->count() > 0)
        <tfoot>
            <tr style="background-color: #f0f0f0;">
                <td colspan="3" class="text-right font-weight-bold">Persentase Ketuntasan Butir Soal:</td>
                @foreach($questions as $question)
                    @php
                        $stats = $questionStats[$question->id];
                        $percentage = $stats['total'] > 0 ? round(($stats['correct'] / $stats['total']) * 100) : 0;
                    @endphp
                    <td class="font-weight-bold" style="font-size: 10px;">
                        @if($question->type == 'essay')
                            -
                        @else
                            {{ $percentage }}%
                        @endif
                    </td>
                @endforeach
                <td colspan="4"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <table style="width: 100%; border: none; margin-top: 40px; page-break-inside: avoid;">
        <tr>
            <td style="width: 60%; border: none;"></td>
            <td style="width: 40%; text-align: center; border: none;">
                <p>............, ........................ {{ date('Y') }}</p>
                <p>Guru Mata Pelajaran</p>
                <br><br><br>
                <p class="font-weight-bold" style="text-decoration: underline; margin-bottom: 0;">{{ $selectedSession->examPackage->subject->creator->name ?? '_______________________' }}</p>
                <p style="margin-top: 2px;">NIP. ....................................</p>
            </td>
        </tr>
    </table>

</body>
</html>
