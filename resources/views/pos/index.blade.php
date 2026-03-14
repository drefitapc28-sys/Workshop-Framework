@extends('layouts.app')
@section('content')

<style>
    /* ── Layout full width ── */
    .pos-wrap { padding: 10px 0; max-width: 750px; }

    h2 { margin-bottom: 20px; font-size: 1.4rem; font-weight: 700; }

    /* ── Form Input ── */
    .form-group { margin-bottom: 12px; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 4px; font-size: 13px; }
    .form-group input {
        width: 100%; padding: 8px 10px;
        border: 1px solid #ccc; border-radius: 6px;
        font-size: 14px; box-sizing: border-box;
    }
    .form-group input:focus { outline: none; border-color: #1a56db; }
    .form-group input[readonly] { background: #fff3cd; }
    .form-group input.found     { background: #d4edda; border-color: #28a745; }

    /* ── Buttons ── */
    .btn-tambah {
        width: 100%; padding: 9px; margin-top: 6px;
        background: #28a745; color: #fff;
        border: none; border-radius: 6px;
        font-size: 14px; font-weight: 600; cursor: pointer;
    }
    .btn-tambah:disabled { background: #a8d5b5; cursor: not-allowed; }
    .btn-bayar {
        padding: 9px 24px;
        background: #1a56db; color: #fff;
        border: none; border-radius: 6px;
        font-size: 14px; font-weight: 600; cursor: pointer;
    }
    .btn-bayar:disabled { background: #a0b4e8; cursor: not-allowed; }
    .btn-del {
        background: #f8d7da; color: #dc3545;
        border: none; border-radius: 4px;
        padding: 3px 9px; cursor: pointer; font-size: 13px;
    }
    .btn-del:hover { background: #dc3545; color: #fff; }

    /* ── Card ── */
    .pos-card {
        background: #fff; border-radius: 8px;
        padding: 18px; margin-bottom: 18px;
        box-shadow: 0 1px 4px rgba(0,0,0,.09);
    }
    .pos-card h5 { margin: 0 0 14px; font-size: 14px; font-weight: 700; }

    /* ── Table ── */
    .tbl { width: 100%; border-collapse: collapse; }
    .tbl thead tr { background: #1a56db; color: #fff; }
    .tbl thead th { padding: 10px 12px; font-size: 12px; font-weight: 600; text-align: left; }
    .tbl tbody td { padding: 9px 12px; border-bottom: 1px solid #f0f0f0; font-size: 13px; vertical-align: middle; }
    .tbl tfoot td { padding: 10px 12px; background: #f5f5f5; font-weight: 700; font-size: 13px; }

    /* ── Qty control ── */
    .qty-wrap { display: flex; align-items: center; gap: 5px; }
    .qty-wrap button {
        width: 26px; height: 26px; border: 1px solid #ccc;
        background: #fff; border-radius: 4px; cursor: pointer; font-weight: 700;
    }
    .qty-wrap button:hover { background: #1a56db; color: #fff; border-color: #1a56db; }
    .qty-wrap input {
        width: 46px; text-align: center;
        border: 1px solid #ccc; border-radius: 4px; padding: 3px;
        font-size: 13px;
    }

    /* ── Total bar ── */
    .total-bar {
        display: flex; justify-content: space-between; align-items: center;
        background: #fff; border-radius: 8px;
        padding: 16px 18px;
        box-shadow: 0 1px 4px rgba(0,0,0,.09);
    }
    .total-amount { font-size: 1.5rem; font-weight: 800; color: #1a56db; }
    .total-label  { font-size: 12px; color: #888; margin-bottom: 2px; }

    /* ── Note kecil ── */
    .tech-note { font-size: 11px; color: #888; margin-top: 4px; }
    .dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; margin-right: 4px; }
    .dot-blue  { background: #1a56db; }
    .dot-green { background: #28a745; }
</style>

<div class="pos-wrap">
    <h2>🛒 Point of Sales</h2>

    {{-- INPUT BARANG --}}
    <div class="pos-card">
        <h5>Input Barang</h5>

        <div class="form-group">
            <label>Kode Barang</label>
            <input type="text" id="kode_barang"
                   placeholder="Ketik kode barang, lalu tekan Enter..."
                   autocomplete="off">
            <div class="tech-note">
                <span class="dot dot-blue"></span>
                Pencarian menggunakan <strong>jQuery AJAX</strong>
            </div>
        </div>

        <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" id="nama_barang" readonly placeholder="Otomatis terisi">
        </div>

        <div class="form-group">
            <label>Harga Satuan</label>
            <input type="text" id="harga_barang" readonly placeholder="Otomatis terisi">
        </div>

        <div class="form-group">
            <label>Jumlah</label>
            <input type="number" id="jumlah" value="1" min="1" disabled>
        </div>

        <button id="btnTambah" class="btn-tambah" disabled>
            + Tambahkan ke Keranjang
        </button>
    </div>

    {{-- TABEL KERANJANG --}}
    <div class="pos-card" style="padding:0; overflow:hidden;">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="cart_tbody">
                <tr id="empty_row">
                    <td colspan="6" style="text-align:center; color:#aaa; padding:24px;">
                        Keranjang kosong. Ketik kode barang di atas lalu tekan Enter.
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right;">TOTAL</td>
                    <td id="total_td">Rp 0</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- TOTAL + TOMBOL BAYAR --}}
    <div class="total-bar">
        <div>
            <div class="total-label">Total Pembayaran</div>
            <div class="total-amount" id="total_display">Rp 0</div>
            <div class="tech-note">
                <span class="dot dot-green"></span>
                Pembayaran disimpan menggunakan <strong>Axios</strong>
            </div>
        </div>
        <button id="btnBayar" class="btn-bayar" disabled>
            💳 Bayar Sekarang
        </button>
    </div>
</div>

@endsection

@push('scripts')
{{-- 
    CATATAN: jQuery & Bootstrap sudah di-load di layouts.app (js-global + DataTables)
    Jadi di sini TIDAK perlu load ulang jQuery atau Bootstrap!
    Cukup tambahkan library yang belum ada:
--}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Utility function untuk format rupiah
const rupiah = (n) => 'Rp ' + parseInt(n).toLocaleString('id-ID');

let cart = {}; // keranjang belanja, key = id_barang

// 1. CARI BARANG — jQuery AJAX (GET) - jQuery sudah tersedia dari layouts.app, langsung pakai $
$('#kode_barang').on('keydown', function (e) {
    if (e.key !== 'Enter') return;

    const kode = $(this).val().trim();
    if (!kode) return;

    // Reset field sebelum request baru
    $('#nama_barang').val('').removeClass('found');
    $('#harga_barang').val('').removeClass('found');
    $('#jumlah').val(1).prop('disabled', true);
    $('#btnTambah').prop('disabled', true);
    $(this).removeData('barang');

    // jQuery AJAX — GET data barang dari server
    $.ajax({
        url    : "{{ url('pos/barang') }}/" + kode,
        method : 'GET',
        success: function (res) {
            if (res.status === 'success') {
                const b = res.data;

                // Isi field nama & harga (read only)
                $('#nama_barang').val(b.nama).addClass('found');
                $('#harga_barang').val(rupiah(b.harga)).addClass('found');

                // Aktifkan jumlah & tombol tambah
                $('#jumlah').val(1).prop('disabled', false);
                $('#btnTambah').prop('disabled', false);

                // Simpan data barang sementara di elemen
                $('#kode_barang').data('barang', b);

                $('#jumlah').focus();
            } else {
                Swal.fire('Tidak Ditemukan', 'Kode barang tidak ada di database.', 'warning');
            }
        },
        error: function (xhr) {
            const msg = xhr.status === 404
                ? 'Kode barang "' + kode + '" tidak ditemukan.'
                : 'Gagal terhubung ke server.';
            Swal.fire('Error', msg, 'error');
        }
    });
});

// 2. TAMBAH KE KERANJANG
$('#btnTambah').on('click', function () {
    const barang = $('#kode_barang').data('barang');
    const jumlah = parseInt($('#jumlah').val());
    if (!barang || jumlah < 1) return;

    if (cart[barang.id_barang]) {
        // Sudah ada → update jumlah & subtotal
        cart[barang.id_barang].jumlah   += jumlah;
        cart[barang.id_barang].subtotal  = cart[barang.id_barang].jumlah * barang.harga;
    } else {
        // Baru → tambahkan ke cart
        cart[barang.id_barang] = {
            ...barang,
            jumlah  : jumlah,
            subtotal: jumlah * barang.harga
        };
    }

    renderCart();
    resetForm();
    $('#kode_barang').focus();
});

// 3. RENDER TABEL KERANJANG
function renderCart() {
    const items = Object.values(cart);
    const total = items.reduce((sum, i) => sum + i.subtotal, 0);

    if (items.length === 0) {
        $('#cart_tbody').html(`
            <tr id="empty_row">
                <td colspan="6" style="text-align:center; color:#aaa; padding:24px;">
                    Keranjang kosong. Ketik kode barang di atas lalu tekan Enter.
                </td>
            </tr>`);
        $('#btnBayar').prop('disabled', true);
    } else {
        $('#cart_tbody').html(items.map(item => `
            <tr>
                <td><code>${item.id_barang}</code></td>
                <td>${item.nama}</td>
                <td>${rupiah(item.harga)}</td>
                <td>
                    <div class="qty-wrap">
                        <button onclick="changeQty('${item.id_barang}', -1)">−</button>
                        <input type="number" value="${item.jumlah}" min="1"
                               onchange="setQty('${item.id_barang}', this.value)">
                        <button onclick="changeQty('${item.id_barang}', 1)">+</button>
                    </div>
                </td>
                <td>${rupiah(item.subtotal)}</td>
                <td>
                    <button class="btn-del" onclick="removeItem('${item.id_barang}')">🗑</button>
                </td>
            </tr>`).join(''));
        $('#btnBayar').prop('disabled', false);
    }

    $('#total_td').text(rupiah(total));
    $('#total_display').text(rupiah(total));
}

function changeQty(id, d) {
    if (!cart[id] || cart[id].jumlah + d < 1) return;
    cart[id].jumlah  += d;
    cart[id].subtotal = cart[id].jumlah * cart[id].harga;
    renderCart();
}

function setQty(id, val) {
    const qty = parseInt(val);
    if (!cart[id] || isNaN(qty) || qty < 1) return;
    cart[id].jumlah   = qty;
    cart[id].subtotal = qty * cart[id].harga;
    renderCart();
}

function removeItem(id) {
    delete cart[id];
    renderCart();
}

function resetForm() {
    $('#kode_barang').val('').removeData('barang');
    $('#nama_barang').val('').removeClass('found');
    $('#harga_barang').val('').removeClass('found');
    $('#jumlah').val(1).prop('disabled', true);
    $('#btnTambah').prop('disabled', true);
}

// 4. BAYAR - Axios (POST) - Axios di-load di atas, tersedia sebagai global 'axios' 
document.getElementById('btnBayar').addEventListener('click', async function () {
    const items = Object.values(cart);
    if (items.length === 0) return;

    const total = items.reduce((sum, i) => sum + i.subtotal, 0);

    // Konfirmasi dulu
    const ok = await Swal.fire({
        title            : 'Konfirmasi Pembayaran',
        html             : `Total: <strong>${rupiah(total)}</strong><br><small>${items.length} jenis barang</small>`,
        icon             : 'question',
        showCancelButton : true,
        confirmButtonText: 'Ya, Bayar!',
        cancelButtonText : 'Batal'
    });
    if (!ok.isConfirmed) return;

    // Loading state
    this.disabled  = true;
    this.innerHTML = '<span>⏳</span> Memproses...';

    try {
        // Axios POST — kirim transaksi ke server
        const res = await axios.post("{{ route('pos.bayar') }}", {
            total : total,
            items : items
        }, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (res.data.status === 'success') {
            await Swal.fire({
                icon : 'success',
                title: 'Pembayaran Berhasil!',
                html : `Transaksi <strong>#${res.data.data.id_penjualan}</strong> telah tersimpan.`
            });

            // Reset semua setelah berhasil
            cart = {};
            renderCart();
            resetForm();
        } else {
            Swal.fire('Gagal!', res.data.message || 'Terjadi kesalahan.', 'error');
        }

    } catch (err) {
        Swal.fire('Error!', 'Gagal menyimpan transaksi ke server.', 'error');
    }

    // Kembalikan tombol
    this.disabled  = false;
    this.innerHTML = '💳 Bayar Sekarang';
});
</script>
@endpush