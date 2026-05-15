<!DOCTYPE html>
<html>
<head>
    <title>Kartu Peserta Ujian</title>
    <style>
        @page {
            size: 215mm 330mm; /* F4 / Folio portrait */
            margin: 10mm;
        }
        body {
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            background-color: #e9ecef; /* Preview bg */
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
        }
        .no-print {
            padding: 15px;
            text-align: center;
            background: #343a40;
            color: #fff;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .container {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }
        .card {
            width: 48%; /* 2 cols */
            height: 92mm; /* Fit 3 rows on F4 Portrait (330mm - 20mm margins) / 3 = ~103mm, we use 92mm safely */
            border: 2px solid #2c3e50;
            border-radius: 8px;
            background-color: #fff;
            box-sizing: border-box;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            margin-bottom: 5px;
        }
        
        /* Premium Header */
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); 
            color: #fff; 
            padding: 8px 10px; 
            border-bottom: 4px solid #f1c40f;
            display: flex;
            align-items: center;
            height: 55px;
            flex-shrink: 0;
        }
        
        .header-text {
            flex: 1;
            text-align: center;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .content {
            display: flex;
            flex-grow: 1;
            padding: 10px 15px;
            font-size: 11px;
        }
        
        .photo-area {
            width: 75px;
            margin-right: 15px;
            text-align: center;
            flex-shrink: 0;
        }
        
        .photo-box {
            width: 75px;
            height: 100px;
            border: 2px solid #bdc3c7;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ecf0f1;
            color: #7f8c8d;
            font-size: 10px;
            font-weight: bold;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .data-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 5px;
        }
        
        .data-table td {
            padding: 2px 0;
            vertical-align: top;
            line-height: 1.2;
        }
        
        .label {
            width: 85px;
            font-weight: 600;
            color: #34495e;
        }
        
        .separator {
            width: 10px;
            text-align: center;
            color: #7f8c8d;
        }

        .footer {
            margin-top: auto;
            text-align: right;
            font-size: 10px;
            position: relative;
            padding-right: 5px;
            padding-bottom: 5px;
        }

        @media print {
            .no-print { display: none; }
            body { background-color: #fff; }
            .container { justify-content: space-between; gap: 0; }
            .card {
                width: 49%;
                height: 92mm;
                margin-bottom: 2mm;
                box-shadow: none;
                border: 2px solid #000;
                border-radius: 5px;
            }
            .header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print">
    <strong>Mode Cetak (Kertas F4/Folio - 6 Buah/Lembar)</strong> - Pastikan Printer set ke <strong>Portrait</strong> dan ukuran kertas <strong>F4 / Folio (215x330mm)</strong>.
    <a href="{{ route('admin.student.index') }}" style="color: #f1c40f; margin-left: 20px; font-weight: bold; text-decoration: none;">[Kembali ke Data Peserta]</a>
</div>

<div class="container">
    @foreach($students as $index => $student)
    <div class="card">
        <!-- Watermark -->
        @if(isset($institution) && ($institution->logo_kiri || $institution->logo))
            <div style="position: absolute; top: 55%; left: 50%; transform: translate(-50%, -50%); opacity: 0.06; z-index: 0; width: 50%; pointer-events: none;">
                  <img src="{{ asset('storage/' . ($institution->logo ?? $institution->logo_kiri)) }}" style="width: 100%;">
            </div>
        @endif

        <!-- Header -->
        <div class="header">
             {{-- Logo Kiri --}}
            <div style="width: 50px; text-align: center;">
                 @if(isset($institution) && ($institution->logo_kiri || $institution->logo))
                    <img src="{{ asset('storage/' . ($institution->logo_kiri ?? $institution->logo)) }}" style="max-height: 45px; max-width: 45px; border-radius: 50%; background: #fff; padding: 2px;">
                @endif
            </div>

            <div class="header-text">
                <div style="font-size: 9px; font-weight: bold; letter-spacing: 1px; opacity: 0.9;">{{ $institution->dinas_name ?? 'DINAS PENDIDIKAN' }}</div>
                <div style="font-size: 13px; font-weight: 800; margin: 2px 0; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">{{ strtoupper(substr($institution->name ?? 'NAMA SEKOLAH', 0, 35)) }}</div>
                <div style="font-size: 9px; background: rgba(0,0,0,0.3); display: inline-block; padding: 2px 8px; border-radius: 8px; font-weight: bold; border: 1px solid rgba(255,255,255,0.2);">KARTU PESERTA UJIAN</div>
            </div>

             {{-- Logo Kanan --}}
             <div style="width: 50px; text-align: center;">
                 @if(isset($institution) && $institution->logo_kanan)
                    <img src="{{ asset('storage/' . $institution->logo_kanan) }}" style="max-height: 45px; max-width: 45px; border-radius: 50%; background: #fff; padding: 2px;">
                @else
                    <div style="width: 50px;"></div>
                @endif
            </div>
        </div>
        
        <!-- Content -->
        <div class="content" style="position: relative; z-index: 1;">
            <div class="photo-area">
                <div class="photo-box">
                    @if($student->photo && file_exists(public_path('storage/' . $student->photo)))
                        <img src="{{ asset('storage/' . $student->photo) }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        PAS FOTO
                        <br>3x4
                    @endif
                </div>
            </div>
            
            <div class="data-area">
                <table class="data-table">
                    <tr>
                        <td class="label">Nama Lengkap</td>
                        <td class="separator">:</td>
                        <td style="font-weight: 800; font-size: 12px; color: #000;">{{ strtoupper($student->name) }}</td>
                    </tr>
                    <tr>
                        <td class="label">NIS / NISN</td>
                        <td class="separator">:</td>
                        <td style="font-weight: 600;">{{ $student->nis ?? '-' }} @if($student->nisn) / {{ $student->nisn }} @endif</td>
                    </tr>
                    <tr>
                        <td class="label">L/P</td>
                        <td class="separator">:</td>
                        <td>{{ $student->gender == 'L' ? 'Laki-laki' : ($student->gender == 'P' ? 'Perempuan' : '-') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kelas/Jurusan</td>
                        <td class="separator">:</td>
                        <td>{{ $student->kelas ?? '-' }} {{ $student->jurusan ? ' - '.$student->jurusan : '' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Ruang Ujian</td>
                        <td class="separator">:</td>
                        <td><span style="background: #ecf0f1; padding: 1px 6px; border-radius: 3px; border: 1px solid #bdc3c7; font-weight: 600;">{{ $student->examRoom->name ?? 'Belum Ditentukan' }}</span></td>
                    </tr>
                    <tr>
                        <td class="label">Password</td>
                        <td class="separator">:</td>
                        <td style="font-family: 'Courier New', Courier, monospace; font-weight: bold; font-size: 13px; letter-spacing: 1px;">{{ $student->password_text ?? $student->nis }}</td>
                    </tr>
                </table>
                
                 <div class="footer">
                    <div style="font-size: 9px; margin-bottom: 2px;">{{ $institution->city ?? 'Kota' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                    
                    <div style="position: relative; height: 55px; width: 100%; margin: 5px 0; display: flex; justify-content: flex-end; padding-right: 15px;">
                        {!! QrCode::size(55)->margin(1)->generate('Dokumen Sah & Valid: ' . ($institution->name ?? 'Sekolah') . ' | ' . \Carbon\Carbon::now()->format('d-m-Y H:i')) !!}
                    </div>
                    <div style="font-size: 9px; font-weight: bold; text-decoration: underline;">{{ $institution->name_head_master ?? 'Kepala Sekolah' }}</div>
                    <div style="font-size: 8px;">NIP. {{ $institution->nip_head_master ?? '-' }}</div>
                 </div>
            </div>
        </div>
    </div>
    
    @endforeach
</div>

</body>
</html>
