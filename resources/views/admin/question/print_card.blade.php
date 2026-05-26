<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Soal</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .kartu-soal {
            width: 100%;
            margin-bottom: 30px;
            page-break-inside: avoid;
            border: 2px solid #000;
            padding: 15px;
            box-sizing: border-box;
        }
        h2.title {
            text-align: center;
            text-transform: uppercase;
            font-size: 14pt;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 15px;
            text-decoration: underline;
        }
        table.header-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 11pt;
            border-collapse: collapse;
        }
        table.header-table td {
            padding: 2px 5px;
            vertical-align: top;
        }
        table.content-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }
        table.content-table th, table.content-table td {
            border: 1px solid #000;
            padding: 10px;
            vertical-align: top;
        }
        .meta-box {
            font-size: 11pt;
        }
        .meta-box strong {
            display: block;
            margin-top: 5px;
            margin-bottom: 3px;
        }
        .meta-box .line {
            border-bottom: 1px dotted #999;
            margin-bottom: 10px;
            min-height: 20px;
        }
        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .badge {
            border: 1px solid #000;
            padding: 3px 8px;
            font-weight: bold;
            font-size: 10pt;
            background: #f0f0f0;
        }
        .question-body {
            font-size: 12pt;
        }
        .question-body img {
            max-width: 100%;
            height: auto;
        }
        .options-list {
            list-style-type: upper-alpha;
            margin-top: 10px;
            padding-left: 20px;
        }
        .options-list li {
            margin-bottom: 5px;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #4e73df;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        .print-btn:hover {
            background: #224abe;
        }
        @media print {
            .print-btn {
                display: none !important;
            }
            .kartu-soal {
                border: 2px solid #000 !important;
            }
        }
    </style>
</head>
<body>

    <button class="print-btn" onclick="window.print()">
        <svg style="width:16px;height:16px;vertical-align:middle;margin-right:5px" viewBox="0 0 24 24">
            <path fill="currentColor" d="M18,3H6V7H18M19,12A1,1 0 0,1 18,11A1,1 0 0,1 19,10A1,1 0 0,1 20,11A1,1 0 0,1 19,12M16,19H8V14H16M19,8H5A3,3 0 0,0 2,11V17H6V21H18V17H22V11A3,3 0 0,0 19,8Z" />
        </svg>
        Cetak Dokumen
    </button>

    @if($questions->isEmpty())
        <div style="text-align: center; padding: 50px;">
            <h3>Tidak ada soal yang ditemukan berdasarkan filter.</h3>
            <p>Silakan tutup jendela ini dan ubah filter pada halaman Bank Soal.</p>
        </div>
    @endif

    @foreach($questions as $question)
        @php
            $kunci = '-';
            if ($question->type === 'multiple_choice' || $question->type === 'multiple_choice_complex') {
                $letters = ['A', 'B', 'C', 'D', 'E'];
                $corrects = [];
                foreach ($question->options as $idx => $opt) {
                    if ($opt->is_correct) {
                        $corrects[] = $letters[$idx] ?? '?';
                    }
                }
                $kunci = implode(', ', $corrects);
            } elseif ($question->type === 'boolean_grid') {
                $kunci = 'Sesuai Pernyataan';
            } elseif ($question->type === 'essay') {
                $kunci = 'Uraian/Esai';
            }
        @endphp

        <div class="kartu-soal">
            <h2 class="title">Kartu Soal</h2>
            
            <table class="header-table">
                <tr>
                    <td width="15%"><strong>Mata Pelajaran</strong></td>
                    <td width="35%">: {{ $question->subject->name ?? '-' }}</td>
                    <td width="15%"><strong>Penyusun</strong></td>
                    <td width="35%">: {{ $question->creator->name ?? 'Admin/Sistem' }}</td>
                </tr>
                <tr>
                    <td><strong>Kurikulum</strong></td>
                    <td>: ................................................</td>
                    <td><strong>Tahun Pelajaran</strong></td>
                    <td>: {{ date('Y') }}/{{ date('Y', strtotime('+1 year')) }}</td>
                </tr>
            </table>

            <table class="content-table">
                <tr>
                    <td width="30%" class="meta-box">
                        <strong>Kompetensi Dasar:</strong>
                        <div class="line"></div>
                        <div class="line"></div>
                        
                        <strong>Materi Pokok:</strong>
                        <div class="line"></div>
                        <div class="line"></div>
                        
                        <strong>Indikator Soal:</strong>
                        <div class="line"></div>
                        <div class="line"></div>
                        <div class="line"></div>
                    </td>
                    <td width="70%">
                        <div class="question-header">
                            <span class="badge">NO. SOAL: {{ $loop->iteration }}</span>
                            <span class="badge">KUNCI: {{ $kunci }}</span>
                            <span class="badge">LEVEL: {{ strtoupper($question->difficulty) }}</span>
                        </div>
                        
                        <div class="question-body">
                            @if($question->readingText)
                                <div style="margin-bottom: 10px; border: 1px dashed #ccc; padding: 10px; background: #fafafa;">
                                    <strong>Petunjuk / Wacana:</strong><br>
                                    {!! $question->readingText->content !!}
                                </div>
                            @endif
                            
                            <div style="margin-bottom: 15px;">
                                {!! $question->content !!}
                            </div>

                            @if($question->type === 'multiple_choice' || $question->type === 'multiple_choice_complex')
                                <ol class="options-list">
                                    @foreach($question->options as $option)
                                        <li>
                                            {!! $option->content !!}
                                            @if($option->is_correct)
                                                <span style="display:inline-block; margin-left: 5px; color: #28a745; font-size: 0.9em; font-weight: bold;">(✓ Benar)</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ol>
                            @elseif($question->type === 'boolean_grid')
                                <table width="100%" border="1" cellpadding="5" style="border-collapse: collapse; margin-top:10px;">
                                    <tr>
                                        <th style="background:#f0f0f0;">Pernyataan</th>
                                        <th width="15%" style="background:#f0f0f0;">Benar</th>
                                        <th width="15%" style="background:#f0f0f0;">Salah</th>
                                    </tr>
                                    @foreach($question->options as $option)
                                    <tr>
                                        <td>{!! $option->content !!}</td>
                                        <td align="center">{!! $option->is_correct ? '<strong>✓</strong>' : '' !!}</td>
                                        <td align="center">{!! !$option->is_correct ? '<strong>✓</strong>' : '' !!}</td>
                                    </tr>
                                    @endforeach
                                </table>
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach

</body>
</html>
