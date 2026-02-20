<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>
@page {
    size: A4 portrait;
    margin: 60px 70px;
}

body {
    font-family: "Times New Roman", serif;
    font-size: 14px;
    line-height: 1.7;
    color: #000;
}

.header {
    text-align: center;
}

.header h3 {
    margin: 0;
    font-size: 15px;
    font-weight: bold;
}

.header h2 {
    margin: 4px 0;
    font-size: 18px;
    font-weight: bold;
}

.header p {
    margin: 2px 0;
    font-size: 13px;
}

.garis {
    border-bottom: 2px solid #000;
    margin-top: 10px;
    margin-bottom: 25px;
}

.section {
    margin-bottom: 15px;
}

.detail {
    margin-left: 40px;
}

.ttd {
    margin-top: 70px;
    width: 100%;
}

.ttd-kanan {
    width: 250px;
    float: right;
    text-align: center;
}

.clear {
    clear: both;
}
</style>

</head>
<body>

<div class="header">
    <h3>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET DAN TEKNOLOGI</h3>
    <h2>UNIVERSITAS AIRLANGGA</h2>
    <h3>FAKULTAS VOKASI</h3>
    <p>Jl. Dharmawangsa dalam, Airlangga, Gubeng, Surabaya</p>
    <p>Telp: (021) 123-4567 | Email: fV@universitasairlangga.ac.id</p>
</div>

<div class="garis"></div>

<div class="section">
    <strong>Nomor</strong> : {{ $nomor }} <br>
    <strong>Perihal</strong> : {{ $perihal }}
</div>

<br>

<div class="section">
    Kepada Yth.<br>
    <strong>{{ $kepada }}</strong><br>
    di Tempat
</div>

<br>

<div class="section">
    Dengan hormat, bersama ini kami mengundang Bapak/Ibu untuk menghadiri acara yang akan dilaksanakan pada:
</div>

<br>

<div class="section detail">
    Hari/Tanggal : {{ $hari_tanggal }}<br>
    Waktu        : {{ $waktu }}<br>
    Tempat       : {{ $tempat }}
</div>

<br>

<div class="section">
    Demikian undangan ini kami sampaikan. Atas perhatian dan kehadiran Bapak/Ibu, kami ucapkan terima kasih.
</div>

<div class="ttd">
    <div class="ttd-kanan">
        {{ $kota_tanggal }}<br><br>
        Dekan,<br><br><br><br>

        <strong>{{ $dekan }}</strong><br>
        NIP. {{ $nip }}
    </div>
</div>

<div class="clear"></div>

</body>
</html>
