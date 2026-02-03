<!DOCTYPE html>
<html>
<head>
    <title>Daftar Hadir Pengawas</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 15mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo {
            width: 80px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            text-transform: uppercase;
        }
        .subtitle {
            text-align: center;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .info-table td {
            padding: 2px; 
            vertical-align: top;
        }
        
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            -webkit-print-color-adjust: exact;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 5px 8px;
        }
        .main-table th {
            background: linear-gradient(to bottom, #004d40, #00695c);
            color: #fff;
            text-align: center;
            font-weight: bold;
            border-color: #004d40;
            text-transform: uppercase;
        }
        .main-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .center { text-align: center; }
        
        .signature-box {
            height: 35px; 
            vertical-align: middle;
        }
        
        .footer {
            margin-top: 30px;
            width: 100%;
            text-align: right;
            page-break-inside: avoid;
        }
        
        @media print {
            .no-print { display: none; }
        }
        .no-print {
            background: #333; color: #fff; padding: 10px; text-align: center; margin-bottom: 20px;
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print">
    <strong>Mode Cetak Daftar Hadir Pengawas</strong>
    <a href="{{ route('admin.report.attendance.index') }}" style="color: #fff; margin-left: 10px;">[Kembali]</a>
</div>

@include('layouts.print_header', ['institution' => $institution])
<div style="text-align: center; margin-bottom: 20px;">
    <h3 style="margin:0; text-decoration: underline;">DAFTAR HADIR PENGAWAS UJIAN</h3>
</div>

<table class="info-table">
    <tr>
        <td width="150">Hari / Tanggal</td>
        <td width="10">:</td>
        <td>{{ $session ? \Carbon\Carbon::parse($session->start_time)->locale('id')->isoFormat('dddd, D MMMM Y') : '..................................................' }}</td>
    </tr>
    <tr>
        <td>Waktu</td>
        <td>:</td>
        <td>{{ $session ? \Carbon\Carbon::parse($session->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($session->end_time)->format('H:i') . ' WIB' : '..................................................' }}</td>
    </tr>
    <tr>
        <td>Mata Pelajaran</td>
        <td>:</td>
        <td>{{ $session ? $session->subject->name : '..................................................' }}</td>
    </tr>
</table>

<table class="main-table">
    <thead>
        <tr>
            <th width="30">NO</th>
            <th>NAMA PENGAWAS</th>
            <th width="150">RUANG</th>
            <th width="180">TANDA TANGAN</th>
            <th width="100">KET</th>
        </tr>
    </thead>
    <tbody>
        @foreach($proctors as $index => $proctor)
        <tr>
            <td class="center">{{ $loop->iteration }}</td>
            <td style="font-weight: bold;">{{ $proctor->name }}</td>
            <td class="center">{{ $proctor->examRoom->name ?? 'Semua Ruangan' }}</td>
            <td class="signature-box">
                @if($loop->iteration % 2 != 0)
                    {{ $loop->iteration }}. 
                @else
                    <div style="text-align: center; padding-left: 50px;">{{ $loop->iteration }}.</div>
                @endif
            </td>
            <td></td>
        </tr>
        @endforeach
        
        {{-- Add empty rows if needed for manual additions --}} 
        @for($i = 0; $i < 3; $i++)
         <tr>
            <td class="center"></td>
            <td></td>
            <td></td>
            <td class="signature-box"></td>
            <td></td>
        </tr>
        @endfor
    </tbody>
</table>

<div class="footer">
    <div style="float: right; width: 220px; text-align: center; position: relative;">
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
</div>

</body>
</html>
