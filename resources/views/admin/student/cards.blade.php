<!DOCTYPE html>
<html>
<head>
    <title>Kartu Peserta Ujian</title>
    <style>
        @page {
            size: A4 portrait; /* Change to Portrait for 8-up */
            margin: 10mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f2f2f2; /* Grey bg for screen preview */
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
        }
        .no-print {
            padding: 10px;
            text-align: center;
            background: #333;
            color: #fff;
            margin-bottom: 20px;
        }
        .container {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px; /* Gap between cards */
        }
        .card {
            width: 48%; /* 2 cols */
            height: 67mm; /* Fit 4 rows on A4 Portrait (297mm - margin) / 4 ~ 68mm */
            border: 1px solid #000;
            border-radius: 6px;
            background-color: #fff;
            box-sizing: border-box;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 2px; /* Slight bottom adjustment */
        }
        
        /* Premium Header Compact */
        .header {
            background: linear-gradient(to right, #004d40, #00796b); 
            color: #fff; 
            padding: 5px; 
            border-bottom: 2px solid #ffd700;
            display: flex;
            align-items: center;
            height: 35px; /* Fixed small header */
            flex-shrink: 0;
        }
        
        .header-text {
            flex: 1;
            text-align: center;
            text-transform: uppercase;
            line-height: 1;
        }

        .content {
            display: flex;
            flex-grow: 1;
            padding: 5px;
            font-size: 10px; /* Base font size small */
        }
        
        .photo-area {
            width: 50px; /* Smaller photo area */
            margin-right: 8px;
            text-align: center;
            flex-shrink: 0;
        }
        
        .photo-box {
            width: 50px;
            height: 65px; /* 3x4 ratio approx */
            border: 1px solid #999;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eee;
            color: #777;
            font-size: 8px;
            box-shadow: 1px 1px 3px rgba(0,0,0,0.1);
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
            font-size: 9px;
            margin-bottom: 2px;
        }
        
        .data-table td {
            padding: 1px 0;
            white-space: nowrap; /* Prevent wrap */
        }
        
        .label {
            width: 55px;
            font-weight: bold;
            color: #444;
        }
        
        .separator {
            width: 5px;
            text-align: center;
        }

        .footer {
            margin-top: auto; /* Push to bottom */
            text-align: right;
            font-size: 8px;
            position: relative;
            padding: 2px 5px;
            height: 45px; /* Fixed height for signature area */
        }

        @media print {
            .no-print { display: none; }
            body { background-color: #fff; }
            .container { display: flex; flex-wrap: wrap; gap: 0; justify-content: space-between; }
            .card {
                width: 49%; /* 2 cols tightly */
                height: 68mm; /* Maximized height */
                margin-bottom: 1mm; /* Tiny margin */
                box-shadow: none;
                border: 1px solid #000;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print">
    <strong>Mode Cetak (Portrait 8 Buah/Lembar)</strong> - Hemat Kertas! Pastikan Printer set ke <strong>Portrait</strong>.
    <a href="{{ route('admin.student.index') }}" style="color: #fff; margin-left: 20px;">[Kembali]</a>
</div>

<div class="container">
    @foreach($students as $index => $student)
    <div class="card">
        <!-- Watermark -->
        @if(isset($institution) && ($institution->logo_kiri || $institution->logo))
            <div style="position: absolute; top: 55%; left: 50%; transform: translate(-50%, -50%); opacity: 0.08; z-index: 0; width: 50%; pointer-events: none;">
                  <img src="{{ asset('storage/' . ($institution->logo ?? $institution->logo_kiri)) }}" style="width: 100%;">
            </div>
        @endif

        <!-- Header -->
        <div class="header">
             {{-- Logo Kiri --}}
            <div style="width: 30px; text-align: center;">
                 @if(isset($institution) && ($institution->logo_kiri || $institution->logo))
                    <img src="{{ asset('storage/' . ($institution->logo_kiri ?? $institution->logo)) }}" style="max-height: 30px; max-width: 30px; border-radius: 50%; background: #fff; padding: 1px;">
                @endif
            </div>

            <div class="header-text">
                <div style="font-size: 7px; font-weight: bold; letter-spacing: 0.5px; opacity: 0.9;">{{ $institution->dinas_name ?? 'DINAS PENDIDIKAN' }}</div>
                <div style="font-size: 10px; font-weight: 800; margin: 1px 0;">{{ substr($institution->name ?? 'SEKOLAH', 0, 30) }}</div>
                <div style="font-size: 8px; background: rgba(0,0,0,0.2); display: inline-block; padding: 0 5px; border-radius: 3px;">KARTU PESERTA</div>
            </div>

             {{-- Logo Kanan --}}
             <div style="width: 30px; text-align: center;">
                 @if(isset($institution) && $institution->logo_kanan)
                    <img src="{{ asset('storage/' . $institution->logo_kanan) }}" style="max-height: 30px; max-width: 30px; border-radius: 50%; background: #fff; padding: 1px;">
                @else
                    <div style="width: 30px;"></div>
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
                        FOTO
                    @endif
                </div>
                <div style="font-size: 8px; margin-top: 2px; font-weight: bold; background: #ddd; border-radius: 3px;">{{ $student->nis }}</div>
            </div>
            
            <div class="data-area">
                <table class="data-table">
                    <tr>
                        <td class="label">Nama</td>
                        <td class="separator">:</td>
                        <td style="font-weight: bold; font-size: 9px; color: #000; white-space: normal;">{{ strtoupper($student->name) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kelas</td>
                        <td class="separator">:</td>
                        <td>{{ $student->kelas ?? '-' }} / {{ $student->jurusan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Ruang</td>
                        <td class="separator">:</td>
                        <td>{{ $student->examRoom->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">User/Pass</td>
                        <td class="separator">:</td>
                        <td style="font-family: monospace;">{{ $student->nis }} / ******</td>
                    </tr>
                </table>
                
                {{-- Footer nested inside content/data-area or separate?
                     Let's put footer below. Space is tight.
                --}}
                 <div style="margin-top: auto; text-align: right; position: relative;">
                    <div style="font-size: 7px; margin-bottom: 2px;">{{ $institution->city ?? 'Kota' }}, {{ date('d-m-Y') }}</div>
                    
                    <div style="position: relative; height: 35px; width: 90px; margin-left: auto;">
                        @if($institution->signature)
                            <img src="{{ asset('storage/' . $institution->signature) }}" style="height: 35px; position: absolute; bottom: 0; right: 10px; z-index: 1;">
                        @endif
                         @if($institution->stamp)
                            <img src="{{ asset('storage/' . $institution->stamp) }}" style="height: 35px; position: absolute; bottom: 0; right: 30px; z-index: 2; opacity: 0.8;">
                        @endif
                    </div>
                    
                    <div style="font-size: 8px; font-weight: bold; text-decoration: underline; margin-top: -5px; position: relative; z-index: 3;">{{ $institution->name_head_master ?? ($institution->head_master ?? 'Kepala Sekolah') }}</div>
                    <div style="font-size: 8px;">NIP. {{ $institution->nip_head_master ?? '-' }}</div>
                 </div>
            </div>
        </div>
    </div>
    
    {{-- Page break every 8 cards --}}
    @if(($index + 1) % 8 == 0)
        <div style="flex-basis: 100%; height: 0; page-break-after: always; visibility: hidden;"></div>
    @endif
    @endforeach
</div> <!-- End Container -->

</body>
</html>
