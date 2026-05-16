<!DOCTYPE html>
<html>
<head>
    <title>Buku Catatan Harian / Kejadian Penting</title>
    <style>
        @page { size: A4; margin: 0; }
        * { box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.3; color: #000; padding: 15mm; margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .doc-title { text-align: center; font-size: 13pt; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
        
        .info-table { margin-bottom: 15px; margin-top: 5px; }
        .info-table td { padding: 3px 5px; vertical-align: top; }
        .info-table .label { width: 150px; }
        .info-table .colon { width: 15px; text-align: center; }
        
        .log-table { border: 1.5px solid #000; margin-top: 10px; }
        .log-table th, .log-table td { border: 1px solid #000; padding: 8px 6px; vertical-align: middle; }
        .log-table th { background-color: #f8f9fa; text-align: center; font-weight: bold; text-transform: uppercase; font-size: 10pt; }
        
        .signature-table { margin-top: 30px; page-break-inside: avoid; }
        .signature-table td { width: 50%; padding: 5px; text-align: center; vertical-align: top; }
        .signature-space { height: 75px; }
        
        .footer-note { font-size: 9pt; font-style: italic; margin-top: 10px; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="background: #e9ecef; padding: 15px; text-align: center; margin-bottom: 20px; border-bottom: 2px solid #dee2e6;">
        <button onclick="window.print()" style="padding: 10px 25px; background: #28a745; color: #fff; border: none; cursor: pointer; border-radius: 5px; font-weight: bold; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <i class="fas fa-print"></i> KLIK UNTUK CETAK DOKUMEN
        </button>
        <p style="margin-top: 8px; color: #6c757d; font-size: 12px; margin-bottom: 0;">Jika masih 2 halaman, pastikan setting <strong>Margins</strong> di browser adalah <strong>None</strong>.</p>
    </div>

    @include('layouts.print_header', ['institution' => $institution])

    <div class="doc-title">BUKU CATATAN HARIAN / KEJADIAN PENTING</div>
    <div class="doc-title" style="font-size: 11pt; margin-bottom: 15px; text-decoration: none;">
        PELAKSANAAN {{ strtoupper($session->examType->name ?? 'UJIAN') }}<br>
        TAHUN PELAJARAN {{ date('Y') }}/{{ date('Y') + 1 }}
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Mata Pelajaran</td>
            <td class="colon">:</td>
            <td class="font-bold">{{ $session->subject->name ?? '-' }}</td>
            <td class="label" style="padding-left: 30px;">Hari / Tanggal</td>
            <td class="colon">:</td>
            <td>{{ \Carbon\Carbon::parse($session->start_time)->translatedFormat('l, d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Ruang Ujian</td>
            <td class="colon">:</td>
            <td class="font-bold">{{ $roomName }}</td>
            <td class="label" style="padding-left: 30px;">Waktu / Sesi</td>
            <td class="colon">:</td>
            <td>{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }} WIB</td>
        </tr>
    </table>

    <p style="margin-bottom: 5px; font-size: 11pt;">Catatan kejadian penting selama pelaksanaan ujian berlangsung:</p>

    <table class="log-table">
        <thead>
            <tr>
                <th width="35">No</th>
                <th width="100">Waktu</th>
                <th width="180">Nama Peserta / Pihak Terkait</th>
                <th>Kejadian / Peristiwa</th>
                <th width="140">Tindakan / Solusi</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 1; $i <= 10; $i++)
            <tr>
                <td height="40" class="text-center font-bold">{{ $i }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endfor
        </tbody>
    </table>

    <div class="footer-note">
        <strong>Keterangan:</strong> Catatan meliputi peserta terlambat, peserta sakit, gangguan teknis, kecurangan, atau hal-hal mendesak lainnya.
    </div>

    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                Ketua Panitia
                <div class="signature-space"></div>
                <strong>( ............................................ )</strong><br>
                NIP. ........................................
            </td>
            <td>
                {{ $institution->city ?? '................' }}, {{ now()->translatedFormat('d F Y') }}<br>
                Pengawas Ruang
                <div class="signature-space"></div>
                <strong>( ............................................ )</strong><br>
                NIP. ........................................
            </td>
        </tr>
    </table>

</body>
</html>
