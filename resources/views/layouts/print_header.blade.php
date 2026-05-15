<table class="header-table" style="width: 100%; border-bottom: 3px double #000; margin-bottom: 20px; font-family: Arial, Helvetica, sans-serif;">
    <tr>
        {{-- Logo Kiri --}}
        <td style="width: 15%; vertical-align: middle; text-align: left;">
            @if(isset($institution) && ($institution->logo_kiri || $institution->logo))
                <img src="{{ asset('storage/' . ($institution->logo_kiri ?? $institution->logo)) }}" style="height: 90px; max-width: 90px; object-fit: contain;">
            @endif
        </td>
        
        {{-- Teks Tengah --}}
        <td style="width: 70%; vertical-align: middle; text-align: center;">
            <div style="font-family: Arial, Helvetica, sans-serif;">
                <div style="font-size: 14pt; font-weight: bold; text-transform: uppercase;">
                    {{ ($institution->dinas_name ?? null) ?: 'PEMERINTAH KABUPATEN ' . strtoupper($institution->city ?? 'KOTA') }}
                </div>
                <div style="font-size: 18pt; font-weight: bold; text-transform: uppercase; margin: 2px 0;">
                    {{ $institution->name ?? 'NAMA SEKOLAH' }}
                </div>
                <div style="font-size: 8pt; margin-top: 5px;">
                    Alamat : {{ $institution->address ?? 'Alamat Sekolah / Lembaga' }} 
                    <br>
                    @if(isset($institution->email) && $institution->email) 
                        E-mail : <span style="color: blue; text-decoration: underline;">{{ $institution->email }}</span> 
                    @endif
                    @if(isset($institution->phone) && $institution->phone) 
                        &nbsp;&nbsp;Telp : {{ $institution->phone }} 
                    @endif
                    @if(isset($institution->npsn) && $institution->npsn) 
                        &nbsp;&nbsp;NPSN : {{ $institution->npsn }} 
                    @endif
                </div>
            </div>
        </td>
        
        {{-- Logo Kanan --}}
        <td style="width: 15%; vertical-align: middle; text-align: right;">
             @if(isset($institution) && $institution->logo_kanan)
                <img src="{{ asset('storage/' . $institution->logo_kanan) }}" style="height: 90px; max-width: 90px; object-fit: contain;">
            @endif
        </td>
    </tr>
</table>
