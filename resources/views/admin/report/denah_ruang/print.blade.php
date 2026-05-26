<!DOCTYPE html>
<html>
<head>
    <title>Denah Ruang Ujian - {{ $room->name }}</title>
    <style>
        @page { size: A4 portrait; margin: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color: #000; margin: 0; padding: 15mm; }
        table { width: 100%; border-collapse: collapse; }
        .header-table { border-bottom: 4px double #000; margin-bottom: 15px; }
        .header-table td { padding: 5px; vertical-align: middle; }
        .logo { max-width: 70px; max-height: 70px; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .school-name { font-size: 14pt; font-weight: bold; text-transform: uppercase; }
        .doc-title { text-align: center; font-size: 13pt; font-weight: bold; text-decoration: underline; margin-bottom: 5px; }
        .sub-title { text-align: center; font-size: 12pt; font-weight: bold; margin-bottom: 10px; }
        
        /* Denah Grid */
        .pengawas-desk {
            border: 2px solid #333;
            background-color: #f0f0f0;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            width: 60%;
            margin: 0 auto 15px auto;
            border-radius: 6px;
        }

        .student-grid {
            display: grid;
            grid-template-columns: repeat({{ request('columns', 4) }}, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .student-desk {
            border: 2px solid #2c3e50;
            padding: 5px;
            text-align: center;
            height: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: 8px;
            page-break-inside: avoid;
            min-width: 0; /* Mencegah kolom melebar jika teks panjang */
        }

        .student-name {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .student-nis {
            font-size: 9pt;
            color: #555;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .empty-desk {
            border: 2px dashed #95a5a6;
            background-color: #f9f9f9;
            color: #95a5a6;
        }

        .board {
            border: 4px solid #8e44ad;
            background-color: #fdfbfd;
            text-align: center;
            padding: 5px;
            font-weight: bold;
            width: 50%;
            margin: 0 auto 10px auto;
            border-radius: 4px;
        }
        
        .door {
            border: 2px solid #e67e22;
            padding: 8px 15px;
            display: inline-block;
            font-weight: bold;
            border-radius: 4px;
            background-color: #fdfbfd;
        }

        .front-layout {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin: 10px 0 10px 0;
        }
        
        .side-stage {
            flex: 1;
        }

        .center-stage {
            flex: 2;
            text-align: center;
        }

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

    @include('layouts.print_header', ['institution' => $institution])

    <div class="doc-title">DENAH TEMPAT DUDUK PESERTA UJIAN</div>
    <div class="sub-title">RUANG: {{ strtoupper($room->name) }}</div>

    <div class="front-layout">
        <!-- Kiri: Pintu -->
        <div class="side-stage">
            <div class="door">PINTU</div>
        </div>

        <!-- Tengah: Papan Tulis & Pengawas -->
        <div class="center-stage">
            <div class="board">PAPAN TULIS</div>
            <div class="pengawas-desk">MEJA PENGAWAS</div>
        </div>
        
        <!-- Kanan: Spacer agar seimbang dengan Pintu -->
        <div class="side-stage"></div>
    </div>

    <div>
        <!-- Meja Peserta -->
        @php
            $columns = (int) request('columns', 4);
            $totalStudents = count($students);
            // Bulatkan ke atas kelipatan kolom untuk row yang rapi
            $totalDesks = ceil($totalStudents / $columns) * $columns;
        @endphp

        <div class="student-grid">
            @for($i = 0; $i < $totalDesks; $i++)
                @if(isset($students[$i]))
                    <div class="student-desk">
                        <div class="student-name">{{ $students[$i]->name }}</div>
                        <div class="student-nis">{{ $students[$i]->nis ?? '-' }} ({{ $students[$i]->group->name ?? '-' }})</div>
                    </div>
                @else
                    <div class="student-desk empty-desk">
                        <div class="student-name">KOSONG</div>
                    </div>
                @endif
            @endfor
        </div>
    </div>

    <div style="margin-top: 30px; display: flex; justify-content: flex-end; page-break-inside: avoid;">
        <div style="width: 250px; text-align: center;">
            <p style="margin-bottom: 50px; font-size: 11pt;">Panitia Ujian,</p>
            <p style="font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 0;">
                (............................................)
            </p>
            <p style="margin-top: 5px; font-size: 10pt;">NIP. .............................</p>
        </div>
    </div>

</body>
</html>
