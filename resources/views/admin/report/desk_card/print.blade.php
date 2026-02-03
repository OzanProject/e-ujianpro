<!DOCTYPE html>
<html>
<head>
    <title>Kartu Meja Peserta Ujian - {{ $roomName }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm; /* Minimal margin */
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }
        .no-print {
            padding: 10px;
            text-align: center;
            background: #333;
            color: #fff;
            margin-bottom: 20px;
            display: block;
        }
        .sheet {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: repeat(4, 1fr);
            gap: 10px;
            height: 270mm; /* Ensure it fits within A4 printable area (297mm - 20mm margins) */
            width: 100%;
            page-break-after: always;
        }
        
        .card {
            border: 1px solid #000;
            position: relative;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            height: 100%; /* Fill grid cell */
        }
        
        .header {
            text-align: center;
            padding: 5px;
            border-bottom: 3px solid #000; /* Thicker separator */
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            height: 45px;
        }
        
        .logo {
            width: 35px; 
            height: 35px;
            margin-right: 8px;
        }
        
        .logo img {
            width: 100%;
            height: auto;
        }
        
        .institution-name {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            line-height: 1.1;
        }
        
        .content {
            flex: 1;
            padding: 8px;
            display: flex;
            align-items: center;
        }
        
        .photo-box {
             width: 70px;
             height: 90px;
             border: 1px solid #999;
             margin-right: 10px;
             display: flex;
             align-items: center;
             justify-content: center;
             background: #f0f0f0;
             color: #999;
             font-size: 9px;
             flex-shrink: 0;
        }
        
        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .details {
            flex: 1;
            overflow: hidden;
        }
        
        .field {
            margin-bottom: 6px;
        }
        
        .field-label {
            font-size: 9px;
            color: #555;
            text-transform: uppercase;
            margin-bottom: 1px;
        }
        
        .field-value {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            border-bottom: 1px solid #ccc;
            padding-bottom: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .room-badge {
            position: absolute;
            top: 40px; /* Below the 35px header */
            right: 0;
            background: #000;
            color: #fff;
            padding: 2px 8px;
            font-weight: bold;
            font-size: 10px;
            z-index: 10;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
            box-shadow: -1px 1px 3px rgba(0,0,0,0.2);
        }

        @media print {
            .no-print { display: none; }
            .sheet { page-break-after: always; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print">
    <strong>Mode Cetak Kartu Meja (Grid 2x4)</strong>
    <a href="{{ route('admin.report.desk_card.index') }}" style="color: #fff; margin-left: 20px;">[Kembali]</a>
</div>

<div class="container">
    @php $chunks = $students->chunk(8); @endphp
    
    @foreach($chunks as $chunk)
    <div class="sheet">
        @foreach($chunk as $student)
        <div class="card">
            <div class="room-badge">R: {{ $student->examRoom->name ?? '-' }}</div>
            
            <!-- Watermark -->
            @if(isset($institution) && ($institution->logo_kiri || $institution->logo))
                <div style="position: absolute; top: 55%; left: 50%; transform: translate(-50%, -50%); opacity: 0.08; z-index: 0; width: 60%; pointer-events: none;">
                      <img src="{{ asset('storage/' . ($institution->logo ?? $institution->logo_kiri)) }}" style="width: 100%;">
                </div>
            @endif

            <div class="header" style="background: linear-gradient(to right, #004d40, #00796b); color: #fff; padding: 5px; border-bottom: 2px solid #ffd700; height: 35px; display: flex; align-items: center; justify-content: center; -webkit-print-color-adjust: exact;">
                 {{-- Logo Kiri --}}
                <div style="width: 35px; text-align: center;">
                    @if(isset($institution) && ($institution->logo_kiri || $institution->logo))
                        <img src="{{ asset('storage/' . ($institution->logo_kiri ?? $institution->logo)) }}" style="max-height: 30px; max-width: 30px; border-radius: 50%; background: #fff; padding: 1px;">
                    @endif
                </div>
                
                <div style="flex: 1; text-align: center; margin: 0 5px;">
                    <div style="font-size: 8px; font-weight: bold; text-transform: uppercase; opacity: 0.9;">{{ $institution->dinas_name ?? 'DINAS PENDIDIKAN' }}</div>
                    <div style="font-size: 11px; font-weight: 800; text-transform: uppercase;">{{ Str::limit($institution->name ?? 'SEKOLAH', 25) }}</div>
                </div>

                {{-- Logo Kanan --}}
                <div style="width: 35px; text-align: center;">
                     @if(isset($institution) && $institution->logo_kanan)
                        <img src="{{ asset('storage/' . $institution->logo_kanan) }}" style="max-height: 30px; max-width: 30px; border-radius: 50%; background: #fff; padding: 1px;">
                    @else
                        <div style="width: 35px;"></div> 
                    @endif
                </div>
            </div>

            <div class="card-title" style="text-align: center; font-size: 10px; font-weight: bold; margin: 5px 0; letter-spacing: 1px; color: #004d40;">KARTU MEJA PESERTA</div>
            
            <div class="content" style="position: relative; z-index: 1;">
                <div class="photo-box" style="border: 2px solid #ccc; box-shadow: 2px 2px 4px rgba(0,0,0,0.1); background: #fff;">
                    @if($student->photo && file_exists(public_path('storage/' . $student->photo)))
                        <img src="{{ asset('storage/' . $student->photo) }}">
                    @else
                        FOTO
                    @endif
                </div>
                
                <div class="details">
                    <div class="field">
                        <div class="field-label">NAMA PESERTA</div>
                        <div class="field-value">{{ Str::limit($student->name, 20) }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">NIS / USERNAME</div>
                        <div class="field-value">{{ $student->nis }}</div>
                    </div>
                     <div class="field">
                        <div class="field-label">PASSWORD LOGIN</div>
                        <div class="field-value">******</div> 
                    </div>
                     <div class="field">
                        <div class="field-label">KELAS</div>
                        <div class="field-value">{{ $student->group->name ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        
        {{-- Fill empty slots to maintain grid if needed, or grid just handles it --}}
    </div>
    @endforeach
</div>

</body>
</html>
