<!DOCTYPE html>
<html>
<head>
    <title>Tata Tertib Pengawas Ujian</title>
    <style>
        @page { size: A4; margin: 15mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11.5pt; line-height: 1.35; color: #000; }
        table { width: 100%; border-collapse: collapse; }
        .header-table { border-bottom: 3px solid #000; margin-bottom: 15px; }
        .header-table td { padding: 3px; vertical-align: middle; }
        .logo { max-width: 75px; max-height: 75px; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .school-name { font-size: 15pt; font-weight: bold; text-transform: uppercase; }
        .doc-title { text-align: center; font-size: 13pt; font-weight: bold; text-decoration: underline; margin-bottom: 15px; }
        
        ol { padding-left: 20px; text-align: justify; margin-top: 5px; margin-bottom: 5px; }
        ol li { margin-bottom: 5px; }
        
        .signature-table { margin-top: 30px; text-align: right; }
        .signature-table td { padding: 5px 10px; }
        .signature-space { height: 70px; }
        
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

    <div class="doc-title">TATA TERTIB PENGAWAS UJIAN</div>

    <ol>
        <li>Dua puluh (20) menit sebelum ujian dimulai, pengawas ruang telah hadir di Ruang Pengawas / Panitia.</li>
        <li>Pengawas ruang menerima penjelasan dan pengarahan dari Ketua Panitia.</li>
        <li>Pengawas ruang menerima bahan ujian yang berupa Token Ujian, Daftar Hadir, dan Berita Acara Pelaksanaan Ujian.</li>
        <li>Lima belas (15) menit sebelum ujian dimulai pengawas ruang memasuki ruang ujian dan memeriksa kesiapan ruang ujian.</li>
        <li>Pengawas ruang meminta peserta ujian untuk mengosongkan meja dan mematuhi tata tertib ujian.</li>
        <li>Pengawas ruang membacakan tata tertib peserta ujian.</li>
        <li>Pengawas ruang membagikan Daftar Hadir untuk ditandatangani oleh peserta ujian.</li>
        <li>Setelah tanda waktu mengerjakan ujian dimulai, pengawas ruang membagikan / merilis Token Ujian kepada peserta.</li>
        <li>Pengawas ruang menjaga ketertiban dan ketenangan suasana ruang ujian.</li>
        <li>Pengawas ruang dilarang merokok, membawa alat komunikasi elektronik, membaca, atau melakukan pekerjaan lain yang dapat mengganggu konsentrasi peserta ujian.</li>
        <li>Pengawas ruang dilarang memberi isyarat, petunjuk, dan bantuan apapun kepada peserta berkaitan dengan jawaban dari soal ujian.</li>
        <li>Lima menit sebelum waktu ujian selesai, pengawas ruang memberi peringatan kepada peserta ujian bahwa waktu tinggal lima menit.</li>
        <li>Setelah waktu ujian selesai, pengawas ruang mempersilahkan peserta untuk berhenti mengerjakan soal dan meninggalkan ruang ujian.</li>
        <li>Pengawas ruang menyusun dan menyerahkan Daftar Hadir Peserta dan Berita Acara Pelaksanaan Ujian kepada Panitia.</li>
    </ol>

    <table class="signature-table">
        <tr>
            <td style="text-align: right; padding-right: 50px;">
                {{ $institution->city ?? 'Kota' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Kepala Sekolah,<br>
                <div class="signature-space"></div>
                <span class="font-bold" style="text-decoration: underline;">{{ $institution->name_head_master ?? ($institution->head_master ?? '...................................') }}</span><br>
                NIP. {{ $institution->nip_head_master ?? '...........................' }}
            </td>
        </tr>
    </table>

</body>
</html>
