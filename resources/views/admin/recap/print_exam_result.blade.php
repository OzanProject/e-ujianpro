<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Hasil Ujian</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2, .header h3, .header p { margin: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { 
            background: linear-gradient(to bottom, #004d40, #00695c) !important; 
            color: #fff !important; 
            text-align: center;
            font-weight: bold;
            -webkit-print-color-adjust: exact;
            border-color: #004d40;
        }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .text-center { text-align: center; }
        .text-success { color: green; }
        .text-danger { color: red; }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        @include('layouts.print_header', ['institution' => $institution])
        <div class="text-center mb-4">
            <h3 class="font-bold underline">REKAP HASIL UJIAN</h3>
        </div>
        <h3 style="margin-top: 15px;">DAFTAR NILAI UJIAN</h3>
        <p>Mata Pelajaran: <strong>{{ $selectedSession->subject->name ?? '-' }}</strong></p>
        <p>Paket/Sesi: {{ $selectedSession->title }}</p>
        <p>Tanggal: {{ \Carbon\Carbon::parse($selectedSession->start_time)->translatedFormat('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th>Nama Peserta</th>
                <th>NIS</th>
                <th>Kelas/Group</th>
                <th class="text-center">Benar</th>
                <th class="text-center">Salah</th>
                <th class="text-center">Nilai</th>
            </tr>
        </thead>
        <tbody>
             @foreach($attempts as $attempt)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $attempt->student->name }}</td>
                    <td>{{ $attempt->student->nis ?? '-' }}</td>
                    <td>
                        {{ $attempt->student->group->name ?? '-' }}
                         @if($attempt->student->kelas)
                            <br><small>({{ $attempt->student->kelas }})</small>
                        @endif
                    </td>
                    @php
                        $correctCount = $attempt->answers->filter(function($ans) {
                            return $ans->is_correct || ($ans->option && $ans->option->is_correct);
                        })->count();
                        
                        $incorrectCount = $attempt->answers->count() - $correctCount;
                    @endphp
                    <td class="text-center text-success">{{ $correctCount }}</td>
                    <td class="text-center text-danger">{{ $incorrectCount }}</td>
                    <td class="text-center"><strong>{{ $attempt->score }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px; float: right; width: 220px; text-align: center; position: relative;">
        <p style="margin-bottom: 5px;">{{ $institution->city ?? 'Kota' }}, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</p>
        <p style="margin-bottom: 40px;">Kepala Sekolah,</p>
        
        <div style="position: relative; height: 80px; width: 100%; margin-top: -30px; margin-bottom: 10px;">
            {{-- Signature --}}
            @if($institution->signature)
                <img src="{{ asset('storage/' . $institution->signature) }}" style="height: 80px; position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); z-index: 1;">
            @endif
            
            {{-- Stamp --}}
            @if($institution->stamp)
                <img src="{{ asset('storage/' . $institution->stamp) }}" style="height: 80px; position: absolute; bottom: 0; left: 20px; z-index: 2; opacity: 0.8;">
            @endif
        </div>

        <p style="margin: 0; font-weight: bold; text-decoration: underline;">{{ $institution->name_head_master ?? ($institution->head_master ?? '.........................') }}</p>
        <p style="margin: 2px 0;">NIP. {{ $institution->nip_head_master ?? '-' }}</p>
    </div>

</body>
</html>
