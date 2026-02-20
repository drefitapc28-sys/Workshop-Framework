<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
@page {
    size: A4 landscape;
    margin: 0;
}

body {
    margin: 0;
    padding: 0;
    font-family: "Times New Roman", serif;
}

.container {
    position: relative;
    width: 100%;
    height: 100%;
}

.bg {
    position: absolute;
    width: 100%;
    height: 100%;
}

.nama {
    position: absolute;
    top: 260px;
    width: 100%;
    text-align: center;
    font-size: 40px;
    font-weight: bold;
}

.peran {
    position: absolute;
    top: 320px;
    width: 100%;
    text-align: center;
    font-size: 20px;
}

.kegiatan {
    position: absolute;
    top: 370px;
    width: 70%;
    left: 15%;
    text-align: center;
    font-size: 15px;
    line-height: 1.6;
}

.nomor {
    position: absolute;
    bottom: 40px;
    right: 60px;
    font-size: 12px;
}

</style>
</head>
<body>

<div class="container">

    <img src="{{ public_path('assets/images/sertifikat_bg.png') }}" class="bg">

    <div class="nama">{{ $nama }}</div>

    <div class="peran">
        Atas partisipasinya sebagai:<br>
        <strong>{{ $peran }}</strong>
    </div>

    <div class="kegiatan">
        "{{ $kegiatan }}" <br>
        yang diselenggarakan oleh <br>
        <strong>{{ $penyelenggara }}</strong><br>
        <strong>{{ $institusi }}</strong><br>
        pada tanggal {{ $tanggal }}
    </div>

    <div class="nomor">
        No: {{ $nomor_sertifikat }}
    </div>

</div>

</body>
</html>
