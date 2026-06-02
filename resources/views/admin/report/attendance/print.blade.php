<!DOCTYPE html>
<html>
<head>
    <title>Daftar Hadir Ujian</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo {
            width: 80px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            text-transform: uppercase;
        }
        .subtitle {
            text-align: center;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .info-table td {
            padding: 2px; 
            vertical-align: top;
        }
        
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 5px 8px;
        }
        .main-table th {
            background: linear-gradient(to bottom, #004d40, #00695c); /* Dark Green Theme */
            color: #fff;
            text-align: center;
            font-weight: bold;
            border-color: #004d40;
            text-transform: uppercase;
        }
        .main-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .main-table {
            -webkit-print-color-adjust: exact;
        }
        .center { text-align: center; }
        
        .signature-box {
            height: 30px; 
            vertical-align: middle;
        }
        
        thead { display: table-header-group; }
        tfoot { display: table-row-group; }
        tr { page-break-inside: avoid; }
        .footer { page-break-inside: avoid; margin-top: 30px; width: 100%; text-align: right; }
        
        /* Master Table for Margins */
        .page-container { width: 100%; border: none; border-collapse: collapse; }
        .page-header-space { height: 10mm; border: none; padding: 0; }
        .page-footer-space { height: 10mm; border: none; padding: 0; }
        .page-content-cell { padding: 0 15mm; border: none; }
        
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        .no-print {
            background: #333; color: #fff; padding: 10px; text-align: center; margin-bottom: 20px;
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print">
    <strong>Mode Cetak Daftar Hadir</strong>
    <a href="{{ route('admin.report.attendance.index') }}" style="color: #fff; margin-left: 10px;">[Kembali]</a>
</div>

@foreach($targetSessions as $sessionIndex => $session)
@foreach($studentsByRoom as $roomId => $roomStudents)
@php
    $filteredStudents = $roomStudents;
@endphp

@if($filteredStudents->count() > 0)
@php
    $chunks = $filteredStudents->chunk(14);
    $totalChunks = $chunks->count();
    $roomName = $roomId === '' ? 'Belum Ada Ruangan' : ($filteredStudents->first()->examRoom->name ?? 'Semua Ruangan');
@endphp
@foreach($chunks as $chunkIndex => $chunk)
<table class="page-container" style="{{ !($loop->parent->parent->last && $loop->parent->last && $loop->last) ? 'page-break-after: always;' : '' }}">
    <thead>
        <tr><td class="page-header-space"></td></tr>
    </thead>
    <tbody>
        <tr><td class="page-content-cell">

            @include('layouts.print_header', ['institution' => $institution])
            <div style="text-align: center; margin-bottom: 20px;">
                <h3 style="margin:0; text-decoration: underline;">DAFTAR HADIR PESERTA UJIAN</h3>
            </div>
            
            <table class="info-table">
                <tr>
                    <td width="150">Mata Pelajaran</td>
                    <td width="10">:</td>
                    <td>{{ $session ? $session->subject->name : '..................................................' }}</td>
                    
                    <td width="100">Hari / Tanggal</td>
                    <td width="10">:</td>
                    <td>{{ $session ? $session->start_time->isoFormat('dddd, D MMMM Y') : '..................................................' }}</td>
                </tr>
                <tr>
                    <td>Kelas / Ruang</td>
                    <td>:</td>
                    <td>{{ $roomName }}</td>
                    
                    <td>Waktu</td>
                    <td>:</td>
                    <td>{{ $session ? $session->start_time->format('H:i') . ' - ' . $session->end_time->format('H:i') : '..................................................' }}</td>
                </tr>
            </table>
            
            <table class="main-table">
                <thead>
                    <tr>
                        <th width="30">NO</th>
                        <th width="100">NO PESERTA / NIS</th>
                        <th>NAMA PESERTA</th>
                        <th width="180">TANDA TANGAN</th>
                        <th width="80">KET</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chunk as $index => $student)
                    @php
                        $globalIndex = ($chunkIndex * 14) + $loop->iteration;
                    @endphp
                    <tr>
                        <td class="center">{{ $globalIndex }}</td>
                        <td class="center">{{ $student->participant_number ? $student->participant_number : $student->nis }}</td>
                        <td>{{ strtoupper($student->name) }}</td>
                        <td class="signature-box">
                            @if($globalIndex % 2 != 0)
                                {{ $globalIndex }}. 
                            @else
                                <div style="text-align: center; padding-left: 50px;">{{ $globalIndex }}.</div>
                            @endif
                        </td>
                        <td></td>
                    </tr>
                    @endforeach
                    
                    {{-- Pad the remaining rows dynamically so every page has exactly 14 rows --}}
                    @php
                        $emptyRowsNeeded = 14 - $chunk->count();
                    @endphp
                    @if($emptyRowsNeeded > 0)
                        @for($i = 0; $i < $emptyRowsNeeded; $i++)
                        @php
                            $emptyGlobalIndex = ($chunkIndex * 14) + $chunk->count() + $i + 1;
                        @endphp
                         <tr>
                            <td class="center">{{ $emptyGlobalIndex }}</td>
                            <td class="center"></td>
                            <td></td>
                            <td class="signature-box">
                                @if($emptyGlobalIndex % 2 != 0)
                                    {{ $emptyGlobalIndex }}. 
                                @else
                                    <div style="text-align: center; padding-left: 50px;">{{ $emptyGlobalIndex }}.</div>
                                @endif
                            </td>
                            <td></td>
                        </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
            
            @if($chunkIndex == $totalChunks - 1)
            <div class="footer" style="margin-top: 15px;">
                <div style="float: right; width: 220px; text-align: center; position: relative;">
                    <p style="margin-bottom: 5px;">{{ $institution->city ?? 'Kota' }}, {{ (request('print_date') ? \Carbon\Carbon::parse(request('print_date')) : \Carbon\Carbon::now())->locale('id')->isoFormat('D MMMM Y') }}</p>
                    <p style="margin-bottom: 40px;">Pengawas Ruang,</p>
                    
                    <div style="position: relative; height: 80px; width: 100%; margin-top: -30px; margin-bottom: 10px;">
                         {{-- Typically signed by Invigilator manual, but if user wants dynamic headmaster/admin signature optionally: --}}
                         {{-- Leaving space for manual signature as requested "Pengawas Ruang" usually implies manual. --}}
                         <div style="height: 80px; width: 100%;"></div>
                    </div>
            
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">( ..................................................... )</p>
                    <p style="margin: 2px 0;">NIP. ........................................</p>
                </div>
            </div>
            @endif

        </td></tr>
    </tbody>
    <tfoot>
        <tr><td class="page-footer-space"></td></tr>
    </tfoot>
</table>
@endforeach
@endif
@endforeach
@endforeach

</body>
</html>
