<table class="header-table" style="width: 100%; border-bottom: 3px double #000; margin-bottom: 20px;">
    <tr>
        {{-- Logo Kiri --}}
        <td style="width: 15%; vertical-align: middle; text-align: center;">
            @if(isset($institution) && ($institution->logo_kiri || $institution->logo))
                <img src="{{ asset('storage/' . ($institution->logo_kiri ?? $institution->logo)) }}" style="height: 100px; max-width: 100px; object-fit: contain;">
            @endif
        </td>
        
        {{-- Teks Tengah --}}
        <td style="width: 70%; vertical-align: middle; text-align: center;">
            <div style="font-family: Arial, sans-serif;">
                <div style="font-size: 18px; font-weight: bold; text-transform: uppercase;">{{ $institution->dinas_name ?? 'PEMERINTAH KABUPATEN ' . strtoupper($institution->city ?? 'KOTA') }}</div>
                <div style="font-size: 24px; font-weight: bold; margin: 5px 0;">{{ $institution->name ?? 'SEKOLAH DEMO' }}</div>
                <div style="font-size: 12px; font-style: italic;">{{ $institution->address ?? 'Alamat Sekolah' }}</div>
                <div style="font-size: 12px;">
                    @if($institution->email) Email: {{ $institution->email }} @endif
                    @if($institution->phone) | Telp: {{ $institution->phone }} @endif
                    @if($institution->npsn) | NPSN: {{ $institution->npsn }} @endif
                </div>
            </div>
        </td>
        
        {{-- Logo Kanan --}}
        <td style="width: 15%; vertical-align: middle; text-align: center;">
             @if(isset($institution) && $institution->logo_kanan)
                <img src="{{ asset('storage/' . $institution->logo_kanan) }}" style="height: 100px; max-width: 100px; object-fit: contain;">
            @endif
        </td>
    </tr>
</table>
