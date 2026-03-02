<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Tag Harga UMKM</title>

<style>
@page {
    margin: 0.2cm;
}

body {
    font-family: Arial, sans-serif;
    font-size: 8px;
    margin: 0;
    padding: 0;
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0.15cm; 
    table-layout: fixed;
}

td {
    width: 3.3cm;
    height: 2.6cm;
    border: 0.5px solid #000;
    text-align: center;
    vertical-align: middle;
    padding: 0.15cm;
    box-sizing: border-box;
}

.label-id {
    font-weight: bold;
    font-size: 8px;
    margin-bottom: 2px;
}

.label-nama {
    font-size: 7px;
    margin-bottom: 2px;
}

.label-harga-text {
    font-size: 6px;
}

.label-harga {
    font-weight: bold;
    font-size: 11px;
    color: #000;
}
</style>
</head>

<body>

<table>
@for ($row = 0; $row < 8; $row++)
    <tr>
        @for ($col = 0; $col < 5; $col++)
            @php
                $index = ($row * 5) + $col + 1;
                $b = $labels[$index] ?? null;
            @endphp

            <td>
                @if($b)
                    <div class="label-id">{{ $b->id_barang }}</div>
                    <div class="label-nama">{{ $b->nama }}</div>
                    <div class="label-harga-text">Harga</div>
                    <div class="label-harga">
                        Rp {{ number_format($b->harga, 0, ',', '.') }}
                    </div>
                @endif
            </td>

        @endfor
    </tr>
@endfor
</table>

</body>
</html>