<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tata Tertib Peserta Ujian</title>
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
        
        ol.sub-rules {
            padding-left: 20px;
            margin-top: 5px;
        }
        ol.sub-rules li {
            margin-bottom: 2px;
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

    <div class="doc-title">TATA TERTIB PESERTA UJIAN</div>

    <ol class="main-rules">
        <li>Peserta ujian memasuki ruangan setelah tanda masuk dibunyikan, yakni 15 (lima belas) menit sebelum ujian dimulai.</li>
        <li>Peserta ujian yang terlambat hadir hanya diperkenankan mengikuti ujian setelah mendapat izin dari ketua panitia, tanpa diberi perpanjangan waktu.</li>
        <li>Peserta ujian dilarang membawa alat komunikasi elektronik dan kalkulator ke Sekolah.</li>
        <li>Tas, buku, dan catatan dalam bentuk apapun dikumpulkan di depan kelas di samping pengawas ruang.</li>
        <li>Peserta ujian membawa alat tulis masing-masing berupa pensil, penghapus, penggaris, dan kartu tanda peserta ujian.</li>
        <li>Peserta ujian mengisi daftar hadir dengan menggunakan pulpen/bolpoin yang disediakan oleh pengawas ruang.</li>
        <li>Peserta ujian yang memerlukan penjelasan dapat bertanya kepada pengawas ruang dengan cara mengacungkan tangan terlebih dahulu.</li>
        <li>Peserta ujian yang akan meninggalkan ruangan selama ujian berlangsung, harus mendapat izin dari pengawas ruang.</li>
        <li>Peserta ujian yang telah selesai mengerjakan soal sebelum waktu ujian berakhir, diperbolehkan meninggalkan ruangan dengan menyerahkan lembar jawaban dan naskah soal kepada pengawas.</li>
        <li>Peserta ujian dilarang:
            <ol type="a" class="sub-rules">
                <li>Menanyakan jawaban soal kepada siapapun.</li>
                <li>Bekerjasama dengan peserta lain.</li>
                <li>Memberi atau menerima bantuan dalam menjawab soal.</li>
                <li>Memperlihatkan pekerjaan sendiri kepada peserta lain atau melihat pekerjaan peserta lain.</li>
                <li>Membawa naskah soal dan lembar jawaban keluar dari ruang ujian.</li>
                <li>Menggantikan atau digantikan oleh orang lain.</li>
            </ol>
        </li>
        <li>Peserta ujian yang melanggar tata tertib ujian, diberi peringatan/teguran oleh pengawas ruang ujian dan dicatat dalam berita acara.</li>
    </ol>

    <div class="signature-section">
        <div class="signature-table">
            <p style="margin: 0;">{{ $institution->city ?? 'Kota' }}, {{ (request('print_date') ? \Carbon\Carbon::parse(request('print_date')) : \Carbon\Carbon::now())->translatedFormat('d F Y') }}</p>
            <p style="margin: 0;">Kepala Sekolah,</p>
            
            <div style="margin-top: 10px; display: flex; justify-content: center; align-items: center; min-height: 80px;">
                @if(isset($institution) && ($institution->signature || $institution->stamp))
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->margin(1)->generate('Dokumen Sah & Valid: ' . ($institution->name ?? 'Sekolah') . ' | ' . \Carbon\Carbon::now()->format('d-m-Y H:i')) !!}
                @else
                    <div style="height: 60px;"></div>
                @endif
            </div>
            
            <p style="margin: 0; font-weight: bold; text-decoration: underline;">{{ $institution->name_head_master ?? ($institution->head_master ?? '...................................') }}</p>
            <p style="margin: 0;">NIP. {{ $institution->nip_head_master ?? '...........................' }}</p>
        </div>
    </div>

</body>
</html>