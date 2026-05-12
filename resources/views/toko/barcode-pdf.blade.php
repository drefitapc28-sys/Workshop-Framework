<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Barcode Toko</title>
<style>
    @page { margin: 0; }
    body { font-family: Arial, sans-serif; margin: 0; padding: 10px; text-align: center; }
    .label { width: 100%; }
    .barcode-img { max-width: 100%; height: auto; margin-bottom: 4px; }
    .kode { font-size: 9px; color: #555; margin-bottom: 4px; }
    .nama { font-weight: bold; font-size: 12px; margin-bottom: 2px; }
    .alamat { font-size: 9px; color: #666; }
</style>
</head>
<body>
<div class="label">
    <img src="{{ $barcode }}" class="barcode-img" alt="Barcode">
    <div class="kode">{{ $toko->barcode }}</div>
    <div class="nama">{{ $toko->nama_toko }}</div>
    @if($toko->alamat)
        <div class="alamat">{{ $toko->alamat }}</div>
    @endif
</div>
</body>
</html>