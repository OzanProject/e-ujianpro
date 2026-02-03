<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Ujian - {{ $institution->name ?? 'Sekolah' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 15mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #333;
            -webkit-print-color-adjust: exact;
        }
        
        /* Premium Table */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .main-table th, .main-table td {
            border: 1px solid #ddd;
            padding: 10px 8px;
        }
        .main-table th {
            background: linear-gradient(to bottom, #004d40, #00695c); /* Dark Green Theme */
            color: #fff;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            border-color: #004d40;
        }
        .main-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .main-table tr:hover {
            background-color: #f1f1f1;
        }
        .center { text-align: center; }
        
        .title-section {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #ffd700; /* Gold Line */
            padding-bottom: 10px;
        }
        .title-section h3 {
            margin: 0;
            color: #004d40;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .title-section h5 {
            margin: 5px 0 0;
            color: #555;
            font-weight: normal;
        }

        .footer {
            margin-top: 40px;
            width: 100%;
            page-break-inside: avoid;
        }
        
        @media print {
            .no-print { display: none; }
            .main-table { box-shadow: none; }
        }
        .no-print {
            background: #333; color: #fff; padding: 10px; text-align: center; margin-bottom: 20px;
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print">
    <strong>Mode Cetak Jadwal Ujian</strong>
    <a href="{{ route('admin.report.exam_schedule') }}" style="color: #fff; margin-left: 10px;">[Kembali]</a>
</div>

@include('layouts.print_header', ['institution' => $institution])

<div class="title-section">
    <h3>JADWAL PELAKSANAAN UJIAN</h3>
    <h5>Periode: {{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMMM Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMMM Y') }}</h5>
</div>

<table class="main-table">
    <thead>
        <tr>
            <th width="40">NO</th>
            <th>MATA PELAJARAN</th>
            <th>PAKET SOAL</th>
            <th>WAKTU PELAKSANAAN</th>
            <th width="100">DURASI</th>
            <th width="120">TOKEN</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sessions as $session)
        <tr>
            <td class="center">{{ $loop->iteration }}</td>
            <td style="font-weight: bold; color: #004d40;">{{ $session->subject->name ?? '-' }}</td>
            <td>{{ $session->examPackage->title ?? 'Semua Soal' }}</td>
            <td>
                <div style="font-weight: bold;">{{ \Carbon\Carbon::parse($session->start_time)->locale('id')->isoFormat('dddd, D MMMM Y') }}</div>
                <small style="color: #666;">{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }} WIB</small>
            </td>
            <td class="center">{{ $session->duration }} Menit</td>
            <td class="center" style="font-family: monospace; font-size: 14px; font-weight: bold; color: #d32f2f; background: #fff3e0;">{{ $session->token }}</td>
        </tr>
        @endforeach
        
        @if($sessions->count() == 0)
        <tr>
            <td colspan="6" class="center" style="padding: 20px; color: #777;">Tidak ada jadwal pada rentang tanggal ini.</td>
        </tr>
        @endif
    </tbody>
</table>

<div class="footer">
    <div style="float: right; width: 220px; text-align: center; position: relative;">
        <p style="margin-bottom: 5px;">{{ $institution->city ?? 'Kota' }}, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</p>
        <p style="margin-bottom: 40px;">Mengetahui,<br>Kepala Sekolah</p>
        
        <div style="position: relative; height: 80px; width: 100%; margin-top: -30px; margin-bottom: 10px;">
            {{-- Signature --}}
            @if($institution->signature)
                <img src="{{ asset('storage/' . $institution->signature) }}" style="height: 80px; position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); z-index: 1;">
            @endif
            
            {{-- Stamp (Overlapping left side of signature) --}}
            @if($institution->stamp)
                <img src="{{ asset('storage/' . $institution->stamp) }}" style="height: 80px; position: absolute; bottom: 0; left: 20px; z-index: 2; opacity: 0.8;">
            @endif
        </div>

        <p style="margin: 0; font-weight: bold; text-decoration: underline;">{{ $institution->name_head_master ?? ($institution->head_master ?? '.........................') }}</p>
        <p style="margin: 2px 0;">NIP. {{ $institution->nip_head_master ?? '-' }}</p>
    </div>
</div>

</body>
</html>
