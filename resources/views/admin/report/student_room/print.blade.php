<!DOCTYPE html>
<html>
<head>
    <title>Daftar Peserta Ruang - {{ $room->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #000;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
        }
        
        table { width: 100%; border-collapse: collapse; }
        
        /* Master Table for Margins */
        .page-container { width: 100%; border: none; border-collapse: collapse; }
        .page-header-space { height: 12mm; border: none; padding: 0; }
        .page-footer-space { height: 12mm; border: none; padding: 0; }
        .page-content-cell { padding: 0 15mm; border: none; }

        .title-section { text-align: center; margin-bottom: 15px; }
        .title-section h3 { margin: 0; font-size: 13pt; text-transform: uppercase; }
        .title-section p { margin: 3px 0 0; font-size: 11pt; }

        .main-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .main-table th, .main-table td { border: 1px solid #000; padding: 6px 5px; font-size: 10pt; }
        .main-table th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        
        thead { display: table-header-group; }
        tfoot { display: table-row-group; }
        tr { page-break-inside: avoid; }

        .footer-sign { margin-top: 20px; width: 100%; font-size: 11pt; }
        .footer-sign td { width: 33%; vertical-align: top; text-align: center; }

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
    <strong>Mode Cetak: Daftar Peserta Per Ruangan</strong>
    <button onclick="window.print()" style="margin-left: 20px; cursor: pointer;">Cetak Sekarang</button>
</div>

<table class="page-container">
    <thead>
        <tr><td class="page-header-space"></td></tr>
    </thead>
    <tbody>
        <tr><td class="page-content-cell">

            @include('layouts.print_header', ['institution' => $institution])

            <div class="title-section">
                <h3>DAFTAR PESERTA UJIAN PER RUANGAN</h3>
                <p>RUANGAN: <strong>{{ strtoupper($room->name) }}</strong></p>
            </div>

            <table class="main-table">
                <thead>
                    <tr>
                        <th width="40">NO</th>
                        <th width="150">NOMOR PESERTA (NIS)</th>
                        <th>NAMA LENGKAP</th>
                        <th width="120">NISN</th>
                        <th width="150">KELAS / JURUSAN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($room->students as $student)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center" style="font-family: monospace;">{{ $student->nis }}</td>
                        <td>{{ strtoupper($student->name) }}</td>
                        <td class="text-center">{{ $student->nisn ?? '-' }}</td>
                        <td class="text-center">{{ $student->kelas }} {{ $student->jurusan }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="footer-sign">
                <tr>
                    <td></td>
                    <td></td>
                    <td>
                        <p>{{ $institution->city ?? 'Kota' }}, {{ (request('print_date') ? \Carbon\Carbon::parse(request('print_date')) : \Carbon\Carbon::now())->translatedFormat('d F Y') }}</p>
                        <p style="margin-bottom: 45px;">Kepala Sekolah,</p>
                        
                        <div style="position: relative; height: 75px; width: 100%; margin-top: -40px; margin-bottom: 5px; display: flex; justify-content: center; align-items: center;">
                            @if(isset($institution) && ($institution->signature || $institution->stamp))
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(75)->margin(1)->generate('Dokumen Sah & Valid: ' . ($institution->name ?? 'Sekolah') . ' | ' . \Carbon\Carbon::now()->format('d-m-Y H:i')) !!}
                            @else
                                <div style="height: 50px;"></div>
                            @endif
                        </div>

                        <p style="margin: 0; font-weight: bold; text-decoration: underline;">{{ $institution->head_master ?? '................................' }}</p>
                        <p style="margin: 0;">NIP. {{ $institution->nip_head_master ?? '-' }}</p>
                    </td>
                </tr>
            </table>

        </td></tr>
    </tbody>
    <tfoot>
        <tr><td class="page-footer-space"></td></tr>
    </tfoot>
</table>

</body>
</html>
