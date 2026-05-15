<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tata Tertib Pengawas Ujian</title>
    <style>
        @page { size: A4; margin: 0; }
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 10pt; 
            line-height: 1.5; 
            color: #000; 
            margin: 0;
            padding: 15mm;
        }
        .header-table { 
            border-bottom: 3px double #000; 
            margin-bottom: 10px !important; 
        }
        .header-table td { padding: 2px; vertical-align: middle; }
        .logo { max-width: 55px; max-height: 55px; }
        
        .doc-title { 
            text-align: center; 
            font-size: 12pt; 
            font-weight: bold; 
            text-decoration: underline; 
            margin-bottom: 15px; 
            text-transform: uppercase;
        }
        
        ol.main-rules { 
            padding-left: 20px; 
            text-align: justify; 
            margin: 0;
        }
        ol.main-rules > li { 
            margin-bottom: 8px; 
        }
        
        .signature-section {
            margin-top: 20px;
            width: 100%;
        }
        .signature-table {
            float: right;
            width: 250px;
            text-align: center;
        }
        .qr-code-box {
            margin: 5px 0;
            display: flex;
            justify-content: center;
        }
        
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; }
        }
        .no-print {
            background: #f8f9fa; padding: 10px; text-align: center; margin-bottom: 10px; border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <button onclick="window.print()" style="padding: 5px 15px; cursor: pointer;">Cetak Dokumen</button>
    </div>

    @include('layouts.print_header', ['institution' => $institution])

    <div class="doc-title">TATA TERTIB PENGAWAS UJIAN</div>

    <ol class="main-rules">
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

    <div class="signature-section">
        <div class="signature-table">
            <p style="margin: 0;">{{ $institution->city ?? 'Kota' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p style="margin: 0;">Kepala Sekolah,</p>
            
            <div style="margin-top: 10px;">
                {!! QrCode::size(80)->margin(1)->generate('Dokumen Sah & Valid: ' . ($institution->name ?? 'Sekolah') . ' | ' . \Carbon\Carbon::now()->format('d-m-Y H:i')) !!}
            </div>
            
            <p style="margin: 0; font-weight: bold; text-decoration: underline;">{{ $institution->name_head_master ?? ($institution->head_master ?? '...................................') }}</p>
            <p style="margin: 0;">NIP. {{ $institution->nip_head_master ?? '...........................' }}</p>
        </div>
    </div>

</body>
</html>
