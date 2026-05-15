<!DOCTYPE html>
<html>
<head>
    <title>Tata Tertib Peserta Ujian</title>
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

    <div class="doc-title">TATA TERTIB PESERTA UJIAN</div>

    <ol>
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
            <ol type="a">
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
