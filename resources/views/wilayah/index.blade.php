@extends('layouts.app')
@section('content')

<style>
    /* ── Form elements ── */
    .form-group { margin-bottom: 14px; }
    .form-group label { display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px; }
    .form-group select {
        width: 100%; padding: 8px 10px;
        border: 1px solid #ccc; border-radius: 6px;
        font-size: 14px; background: #fff;
    }
    .form-group select:focus { outline: none; border-color: #1a56db; }
    .form-group select:disabled { background: #f5f5f5; color: #aaa; }

    /* ── Card ── */
    .pos-card {
        background: #fff; border-radius: 8px;
        padding: 20px; margin-bottom: 16px;
        box-shadow: 0 1px 4px rgba(0,0,0,.09);
        max-width: 600px;
    }
    .pos-card h5 { margin: 0 0 16px; font-size: 15px; font-weight: 700; }

    /* ── Result box ── */
    .result-box {
        background: #eff6ff; border: 1px solid #bfdbfe;
        border-radius: 6px; padding: 12px 16px;
        margin-top: 16px; font-size: 13px;
        display: none;
    }
    .result-box h6 { font-weight: 700; color: #1a56db; margin: 0 0 8px; }
    .result-row { display: flex; gap: 8px; padding: 4px 0; border-bottom: 1px dashed #bfdbfe; }
    .result-row:last-child { border-bottom: none; }
    .result-row .lbl { color: #888; min-width: 90px; }

    /* ── Tabs ── */
    .tab-nav { display: flex; gap: 4px; margin-bottom: 20px; border-bottom: 2px solid #e5e7eb; }
    .tab-btn {
        padding: 8px 18px; border: none; background: none;
        font-size: 14px; font-weight: 600; color: #6b7280;
        cursor: pointer; border-bottom: 3px solid transparent; margin-bottom: -2px;
    }
    .tab-btn.active { color: #1a56db; border-bottom-color: #1a56db; }
    .tab-pane { display: none; }
    .tab-pane.active { display: block; }

    /* ── Note ── */
    .tech-note { font-size: 11px; color: #888; margin-top: 16px; }
    .dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; margin-right: 4px; }
    .dot-blue  { background: #1a56db; }
    .dot-green { background: #28a745; }
</style>

<h2 style="font-size:1.3rem; font-weight:700; margin-bottom:20px;">
    🗺️ Wilayah Administrasi Indonesia
</h2>

{{-- TAB NAVIGATION --}}
<div class="tab-nav">
    <button class="tab-btn active" onclick="switchTab('jquery')">
        🔵 jQuery AJAX
    </button>
    <button class="tab-btn" onclick="switchTab('axios')">
        🟢 Axios
    </button>
</div>

{{-- TAB 1 : jQuery AJAX --}}
<div class="tab-pane active" id="tab-jquery">
    <div class="pos-card">
        <h5>Pilih Wilayah</h5>

        <div class="form-group">
            <label>Provinsi</label>
            <select id="jq_provinsi">
                <option value="0">-- Pilih Provinsi --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Kota / Kabupaten</label>
            <select id="jq_kota" disabled>
                <option value="0">-- Pilih Kota --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Kecamatan</label>
            <select id="jq_kecamatan" disabled>
                <option value="0">-- Pilih Kecamatan --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Kelurahan / Desa</label>
            <select id="jq_kelurahan" disabled>
                <option value="0">-- Pilih Kelurahan --</option>
            </select>
        </div>

        {{-- Hasil pilihan --}}
        <div class="result-box" id="jq_result">
            <h6>📍 Alamat Terpilih</h6>
            <div class="result-row"><span class="lbl">Provinsi</span>  <span id="jq_r_prov">—</span></div>
            <div class="result-row"><span class="lbl">Kota</span>      <span id="jq_r_kota">—</span></div>
            <div class="result-row"><span class="lbl">Kecamatan</span> <span id="jq_r_kec">—</span></div>
            <div class="result-row"><span class="lbl">Kelurahan</span> <span id="jq_r_kel">—</span></div>
        </div>

        <div class="tech-note">
            <span class="dot dot-blue"></span>
            Semua request menggunakan <strong>jQuery AJAX</strong>
        </div>
    </div>
</div>

{{-- TAB 2 : Axios --}}
<div class="tab-pane" id="tab-axios">
    <div class="pos-card">
        <h5>Pilih Wilayah</h5>

        <div class="form-group">
            <label>Provinsi</label>
            <select id="ax_provinsi">
                <option value="0">-- Pilih Provinsi --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Kota / Kabupaten</label>
            <select id="ax_kota" disabled>
                <option value="0">-- Pilih Kota --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Kecamatan</label>
            <select id="ax_kecamatan" disabled>
                <option value="0">-- Pilih Kecamatan --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Kelurahan / Desa</label>
            <select id="ax_kelurahan" disabled>
                <option value="0">-- Pilih Kelurahan --</option>
            </select>
        </div>

        {{-- Hasil pilihan --}}
        <div class="result-box" id="ax_result" style="background:#ecfdf5; border-color:#a7f3d0;">
            <h6 style="color:#28a745">📍 Alamat Terpilih</h6>
            <div class="result-row"><span class="lbl">Provinsi</span>  <span id="ax_r_prov">—</span></div>
            <div class="result-row"><span class="lbl">Kota</span>      <span id="ax_r_kota">—</span></div>
            <div class="result-row"><span class="lbl">Kecamatan</span> <span id="ax_r_kec">—</span></div>
            <div class="result-row"><span class="lbl">Kelurahan</span> <span id="ax_r_kel">—</span></div>
        </div>

        <div class="tech-note">
            <span class="dot dot-green"></span>
            Semua request menggunakan <strong>Axios</strong>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{--
    CATATAN PENTING:
    jQuery & Bootstrap sudah di-load di layouts.app (js-global).
    Jangan load ulang di sini — akan menyebabkan sidebar conflict!
    Cukup load Axios dan SweetAlert2 saja.
--}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Tab switcher sederhana 
function switchTab(tab) {
    // Sembunyikan semua pane
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

    // Tampilkan yang dipilih
    document.getElementById('tab-' + tab).classList.add('active');
    event.target.classList.add('active');

    // Load provinsi Axios saat pertama kali tab dibuka
    if (tab === 'axios' && !axLoaded) loadAxProvinsi();
}

// SECTION 1 - jQuery AJAX - jQuery sudah tersedia dari layouts.app, langsung pakai $

// Load provinsi saat halaman siap (jQuery)
$(document).ready(function () {
    $.ajax({
        url    : "{{ route('wilayah.provinsi') }}",
        method : 'GET',
        success: function (res) {
            let opt = '<option value="0">-- Pilih Provinsi --</option>';
            res.data.forEach(p => { opt += `<option value="${p.id}">${p.name}</option>`; });
            $('#jq_provinsi').html(opt);
        },
        error: function () {
            Swal.fire('Error', 'Gagal memuat data provinsi.', 'error');
        }
    });
});

// Provinsi berubah → load kota, reset kecamatan & kelurahan (poin d soal)
$('#jq_provinsi').on('change', function () {
    const id = $(this).val();

    // Reset level 2, 3, 4
    $('#jq_kota').html('<option value="0">-- Pilih Kota --</option>').prop('disabled', true);
    $('#jq_kecamatan').html('<option value="0">-- Pilih Kecamatan --</option>').prop('disabled', true);
    $('#jq_kelurahan').html('<option value="0">-- Pilih Kelurahan --</option>').prop('disabled', true);
    $('#jq_result').hide();

    if (id === '0') return;

    $('#jq_kota').html('<option>Memuat...</option>');

    $.ajax({
        url    : "{{ url('wilayah/kota') }}/" + id,
        method : 'GET',
        success: function (res) {
            let opt = '<option value="0">-- Pilih Kota --</option>';
            res.data.forEach(k => { opt += `<option value="${k.id}">${k.name}</option>`; });
            $('#jq_kota').html(opt).prop('disabled', false);

            // Tampilkan result box
            $('#jq_r_prov').text($('#jq_provinsi option:selected').text());
            $('#jq_r_kota').text('—');
            $('#jq_r_kec').text('—');
            $('#jq_r_kel').text('—');
            $('#jq_result').show();
        },
        error: function () { Swal.fire('Error', 'Gagal memuat kota.', 'error'); }
    });
});

// Kota berubah → load kecamatan, reset kelurahan (poin e soal)
$('#jq_kota').on('change', function () {
    const id = $(this).val();

    // Reset level 3 & 4
    $('#jq_kecamatan').html('<option value="0">-- Pilih Kecamatan --</option>').prop('disabled', true);
    $('#jq_kelurahan').html('<option value="0">-- Pilih Kelurahan --</option>').prop('disabled', true);

    if (id === '0') return;

    $('#jq_kecamatan').html('<option>Memuat...</option>');

    $.ajax({
        url    : "{{ url('wilayah/kecamatan') }}/" + id,
        method : 'GET',
        success: function (res) {
            let opt = '<option value="0">-- Pilih Kecamatan --</option>';
            res.data.forEach(k => { opt += `<option value="${k.id}">${k.name}</option>`; });
            $('#jq_kecamatan').html(opt).prop('disabled', false);

            $('#jq_r_kota').text($('#jq_kota option:selected').text());
            $('#jq_r_kec').text('—');
            $('#jq_r_kel').text('—');
        },
        error: function () { Swal.fire('Error', 'Gagal memuat kecamatan.', 'error'); }
    });
});

// Kecamatan berubah → load kelurahan
$('#jq_kecamatan').on('change', function () {
    const id = $(this).val();

    // Reset level 4
    $('#jq_kelurahan').html('<option value="0">-- Pilih Kelurahan --</option>').prop('disabled', true);

    if (id === '0') return;

    $('#jq_kelurahan').html('<option>Memuat...</option>');

    $.ajax({
        url    : "{{ url('wilayah/kelurahan') }}/" + id,
        method : 'GET',
        success: function (res) {
            let opt = '<option value="0">-- Pilih Kelurahan --</option>';
            res.data.forEach(k => { opt += `<option value="${k.id}">${k.name}</option>`; });
            $('#jq_kelurahan').html(opt).prop('disabled', false);

            $('#jq_r_kec').text($('#jq_kecamatan option:selected').text());
            $('#jq_r_kel').text('—');
        },
        error: function () { Swal.fire('Error', 'Gagal memuat kelurahan.', 'error'); }
    });
});

// Kelurahan berubah → update result
$('#jq_kelurahan').on('change', function () {
    if ($(this).val() !== '0') {
        $('#jq_r_kel').text($('#jq_kelurahan option:selected').text());
    }
});

// SECTION 2 — Axios (async/await)

let axLoaded = false;

// Load provinsi Axios (dipanggil saat tab Axios pertama kali dibuka)
async function loadAxProvinsi() {
    axLoaded = true;
    try {
        const res = await axios.get("{{ route('wilayah.provinsi') }}");
        let opt = '<option value="0">-- Pilih Provinsi --</option>';
        res.data.data.forEach(p => { opt += `<option value="${p.id}">${p.name}</option>`; });
        document.getElementById('ax_provinsi').innerHTML = opt;
    } catch {
        Swal.fire('Error', 'Gagal memuat data provinsi.', 'error');
    }
}

// Helper reset select
function resetSelect(id, label) {
    const el = document.getElementById(id);
    el.innerHTML = `<option value="0">-- ${label} --</option>`;
    el.disabled  = true;
}

// Provinsi berubah → load kota, reset kecamatan & kelurahan (poin d)
document.getElementById('ax_provinsi').addEventListener('change', async function () {
    const id = this.value;

    // Reset level 2, 3, 4
    resetSelect('ax_kota',      'Pilih Kota');
    resetSelect('ax_kecamatan', 'Pilih Kecamatan');
    resetSelect('ax_kelurahan', 'Pilih Kelurahan');
    document.getElementById('ax_result').style.display = 'none';

    if (id === '0') return;

    document.getElementById('ax_kota').innerHTML = '<option>Memuat...</option>';

    try {
        const res = await axios.get(`{{ url('wilayah/kota') }}/${id}`);
        let opt = '<option value="0">-- Pilih Kota --</option>';
        res.data.data.forEach(k => { opt += `<option value="${k.id}">${k.name}</option>`; });
        document.getElementById('ax_kota').innerHTML = opt;
        document.getElementById('ax_kota').disabled  = false;

        // Tampilkan result box
        document.getElementById('ax_r_prov').textContent = this.options[this.selectedIndex].text;
        document.getElementById('ax_r_kota').textContent = '—';
        document.getElementById('ax_r_kec').textContent  = '—';
        document.getElementById('ax_r_kel').textContent  = '—';
        document.getElementById('ax_result').style.display = 'block';
    } catch {
        Swal.fire('Error', 'Gagal memuat kota.', 'error');
    }
});

// Kota berubah → load kecamatan, reset kelurahan (poin e)
document.getElementById('ax_kota').addEventListener('change', async function () {
    const id = this.value;

    // Reset level 3 & 4
    resetSelect('ax_kecamatan', 'Pilih Kecamatan');
    resetSelect('ax_kelurahan', 'Pilih Kelurahan');

    if (id === '0') return;

    document.getElementById('ax_kecamatan').innerHTML = '<option>Memuat...</option>';

    try {
        const res = await axios.get(`{{ url('wilayah/kecamatan') }}/${id}`);
        let opt = '<option value="0">-- Pilih Kecamatan --</option>';
        res.data.data.forEach(k => { opt += `<option value="${k.id}">${k.name}</option>`; });
        document.getElementById('ax_kecamatan').innerHTML = opt;
        document.getElementById('ax_kecamatan').disabled  = false;

        document.getElementById('ax_r_kota').textContent = this.options[this.selectedIndex].text;
        document.getElementById('ax_r_kec').textContent  = '—';
        document.getElementById('ax_r_kel').textContent  = '—';
    } catch {
        Swal.fire('Error', 'Gagal memuat kecamatan.', 'error');
    }
});

// Kecamatan berubah → load kelurahan
document.getElementById('ax_kecamatan').addEventListener('change', async function () {
    const id = this.value;

    // Reset level 4
    resetSelect('ax_kelurahan', 'Pilih Kelurahan');

    if (id === '0') return;

    document.getElementById('ax_kelurahan').innerHTML = '<option>Memuat...</option>';

    try {
        const res = await axios.get(`{{ url('wilayah/kelurahan') }}/${id}`);
        let opt = '<option value="0">-- Pilih Kelurahan --</option>';
        res.data.data.forEach(k => { opt += `<option value="${k.id}">${k.name}</option>`; });
        document.getElementById('ax_kelurahan').innerHTML = opt;
        document.getElementById('ax_kelurahan').disabled  = false;

        document.getElementById('ax_r_kec').textContent = this.options[this.selectedIndex].text;
        document.getElementById('ax_r_kel').textContent = '—';
    } catch {
        Swal.fire('Error', 'Gagal memuat kelurahan.', 'error');
    }
});

// Kelurahan berubah → update result
document.getElementById('ax_kelurahan').addEventListener('change', function () {
    if (this.value !== '0') {
        document.getElementById('ax_r_kel').textContent = this.options[this.selectedIndex].text;
    }
});
</script>
@endpush