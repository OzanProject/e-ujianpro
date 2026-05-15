<!DOCTYPE html>
<html>
<head>
    <title>Berita Acara Pelaksanaan Ujian</title>
    <style>
        @page { size: A4; margin: 20mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; }
        table { width: 100%; border-collapse: collapse; }
        .header-table { border-bottom: 3px solid #000; margin-bottom: 20px; }
        .header-table td { padding: 5px; vertical-align: middle; }
        .logo { max-width: 80px; max-height: 80px; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .school-name { font-size: 16pt; font-weight: bold; text-transform: uppercase; }
        .doc-title { text-align: center; font-size: 14pt; font-weight: bold; text-decoration: underline; margin-bottom: 20px; }
        
        .content-table td { padding: 4px 8px; vertical-align: top; }
        .content-table .label { width: 200px; }
        .content-table .colon { width: 10px; text-align: center; }
        
        .box-table { border: 1px solid #000; margin: 15px 0; }
        .box-table th, .box-table td { border: 1px solid #000; padding: 8px; }
        
        .signature-table { margin-top: 40px; text-align: center; }
        .signature-table td { width: 33.33%; padding: 10px; }
        .signature-space { height: 80px; }
        
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="background: #f8f9fa; padding: 15px; text-align: center; margin-bottom: 20px; border: 1px solid #ddd;">
        <button onclick="window.print()" style="padding: 8px 15px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 4px;">Cetak Dokumen</button>
    </div>

    <table class="header-table">
        <tr>
            <td width="15%" class="text-center">
                @if($institution && ($institution->logo_kiri || $institution->logo))
                    <img src="{{ asset('storage/' . ($institution->logo_kiri ?? $institution->logo)) }}" class="logo">
                @endif
            </td>
            <td width="70%" class="text-center">
                <div style="font-size: 12pt;">{{ $institution->dinas_name ?? 'DINAS PENDIDIKAN' }}</div>
                <div class="school-name">{{ $institution->name ?? 'NAMA SEKOLAH' }}</div>
                <div style="font-size: 10pt;">{{ $institution->address ?? 'Alamat Sekolah' }}</div>
            </td>
            <td width="15%" class="text-center">
                @if($institution && $institution->logo_kanan)
                    <img src="{{ asset('storage/' . $institution->logo_kanan) }}" class="logo">
                @endif
            </td>
        </tr>
    </table>

    <div class="doc-title" style="margin-bottom: 5px; text-decoration: none;">BERITA ACARA</div>
    <div class="doc-title" style="text-decoration: none; margin-bottom: 20px;">
        PENYELENGGARAAN {{ strtoupper($session->examType->name ?? 'UJIAN') }}<br>
        TAHUN PELAJARAN {{ date('Y') }}/{{ date('Y') + 1 }}
    </div>

    <p style="text-align: justify; line-height: 1.8;">
        Pada hari ini <span class="font-bold">{{ \Carbon\Carbon::parse($session->start_time)->translatedFormat('l') }}</span> 
        Tanggal <span class="font-bold">{{ \Carbon\Carbon::parse($session->start_time)->translatedFormat('d') }}</span> 
        Bulan <span class="font-bold">{{ \Carbon\Carbon::parse($session->start_time)->translatedFormat('F') }}</span> 
        Tahun <span class="font-bold">{{ \Carbon\Carbon::parse($session->start_time)->translatedFormat('Y') }}</span> 
        bertempat di <strong>{{ $institution->name ?? '-' }}</strong> :
    </p>

    <ol style="list-style-type: lower-alpha; text-align: justify; padding-left: 20px; line-height: 1.8;">
        <li>
            Telah dilaksanakan {{ strtoupper($session->examType->name ?? 'UJIAN') }} secara daring dan luring dari pukul 
            <strong>{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}</strong> sampai dengan pukul 
            <strong>{{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</strong> untuk Mata Pelajaran 
            <strong>{{ $session->subject->name ?? '-' }}</strong>
            
            <table class="content-table" style="margin-top: 10px; margin-bottom: 15px; width: 90%;">
                <tr>
                    <td class="label" style="width: 250px;">Ruang / Kelas</td>
                    <td class="colon">:</td>
                    <td><strong>{{ $roomName }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Jumlah peserta seharusnya</td>
                    <td class="colon">:</td>
                    <td>.......... orang</td>
                </tr>
                <tr>
                    <td class="label">Jumlah peserta yang hadir</td>
                    <td class="colon">:</td>
                    <td>.......... orang</td>
                </tr>
                <tr>
                    <td class="label">Jumlah peserta yang tidak hadir</td>
                    <td class="colon">:</td>
                    <td>.......... orang</td>
                </tr>
                <tr>
                    <td colspan="3" style="padding-top: 10px;">Yakni nomor peserta :</td>
                </tr>
                <tr>
                    <td colspan="3" style="line-height: 2;">
                        ........................................................................................................................................................<br>
                        ........................................................................................................................................................<br>
                        ........................................................................................................................................................
                    </td>
                </tr>
            </table>
        </li>
        <li>
            Catatan selama pelaksanaan {{ $session->examType->name ?? 'Ujian' }} :
            <div style="line-height: 2; margin-top: 10px;">
                ..............................................................................................................................................................................<br>
                ..............................................................................................................................................................................<br>
                ..............................................................................................................................................................................
            </div>
        </li>
    </ol>

    <p style="text-align: left; margin-top: 30px;">
        Berita acara ini dibuat dengan sesungguhnya
    </p>

    <table style="width: 100%; text-align: left; margin-top: 20px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <strong style="display: block; margin-bottom: 20px;">Pengawas I</strong>
                <table style="width: 100%;">
                    <tr>
                        <td width="20">1.</td>
                        <td width="100">Tanda tangan</td>
                        <td width="10">:</td>
                        <td>...................................</td>
                    </tr>
                    <tr>
                        <td style="padding-top: 15px;">2.</td>
                        <td style="padding-top: 15px;">Nama</td>
                        <td style="padding-top: 15px;">:</td>
                        <td style="padding-top: 15px;">...................................</td>
                    </tr>
                    <tr>
                        <td style="padding-top: 15px;">3.</td>
                        <td style="padding-top: 15px;">NIP</td>
                        <td style="padding-top: 15px;">:</td>
                        <td style="padding-top: 15px;">...................................</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 20px;">
                Yang membuat berita acara<br>
                <strong style="display: block; margin-bottom: 20px;">Pengawas II</strong>
                <table style="width: 100%;">
                    <tr>
                        <td width="20">1.</td>
                        <td width="100">Tanda tangan</td>
                        <td width="10">:</td>
                        <td>...................................</td>
                    </tr>
                    <tr>
                        <td style="padding-top: 15px;">2.</td>
                        <td style="padding-top: 15px;">Nama</td>
                        <td style="padding-top: 15px;">:</td>
                        <td style="padding-top: 15px;">...................................</td>
                    </tr>
                    <tr>
                        <td style="padding-top: 15px;">3.</td>
                        <td style="padding-top: 15px;">NIP</td>
                        <td style="padding-top: 15px;">:</td>
                        <td style="padding-top: 15px;">...................................</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
