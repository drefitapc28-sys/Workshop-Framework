@extends('layouts.app')

@section('content')
<style>
    body { background: #f0f2f5; }
    .page-header { background: linear-gradient(135deg, #1e3a5f, #2563eb); color: white; padding: 20px 30px; border-radius: 0 0 20px 20px; margin-bottom: 24px; }
    .card-custom { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
    .step-badge { background: #2563eb; color: white; width: 26px; height: 26px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: .8rem; font-weight: bold; margin-right: 8px; }
    .menu-card { border: 2px solid #e5e7eb; border-radius: 12px; cursor: pointer; transition: all .2s; overflow: hidden; }
    .menu-card:hover { border-color: #2563eb; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37,99,235,0.15); }
    .menu-card .menu-img { height: 100px; object-fit: cover; width: 100%; }
    .menu-card .menu-placeholder { height: 100px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; }
    .menu-card .card-body { padding: 10px 12px; }
    .menu-card .nama { font-weight: 600; font-size: .88rem; color: #1e293b; margin-bottom: 4px; }
    .menu-card .harga { color: #2563eb; font-weight: 700; font-size: .88rem; }
    .menu-card .add-btn { background: #2563eb; color: white; font-size: .78rem; padding: 4px 0; text-align: center; }
    .cart-item { background: #f8fafc; border-radius: 10px; padding: 10px 14px; margin-bottom: 8px; border: 1px solid #e2e8f0; }
    .qty-btn { width: 28px; height: 28px; padding: 0; border-radius: 6px; font-size: .9rem; display: inline-flex; align-items: center; justify-content: center; }
    .total-box { background: linear-gradient(135deg, #1e3a5f, #2563eb); color: white; border-radius: 12px; padding: 16px 20px; }
    .btn-pay { background: linear-gradient(135deg, #16a34a, #15803d); color: white; border: none; border-radius: 10px; padding: 12px; font-weight: 700; font-size: 1rem; width: 100%; transition: opacity .2s; }
    .btn-pay:hover { opacity: .9; color: white; }
    #menuLoading, #menuEmpty { padding: 30px 0; text-align: center; color: #94a3b8; }
    .vendor-select { border-radius: 10px; border: 2px solid #e2e8f0; padding: 10px 14px; font-size: .95rem; transition: border-color .2s; }
    .vendor-select:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); outline: none; }
    #cartEmpty { text-align: center; padding: 40px 0; color: #94a3b8; }
</style>

<div class="page-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4 class="mb-0 fw-bold"><i class="bi bi-bag-heart-fill me-2"></i>Kantin Online</h4>
            <small class="opacity-75">Pesan makanan & minuman favoritmu</small>
        </div>
        <!-- <a href="/vendor/login" class="btn btn-outline-light btn-sm">
            <i class="bi bi-shop me-1"></i> Login Vendor -->
        </a>
    </div>
</div>

<div class="container-fluid px-3 px-md-4">
    <div class="row g-3">

        {{-- KIRI: Form Pemesanan --}}
        <div class="col-lg-8">

            {{-- Step 1: Pilih Vendor --}}
            <div class="card card-custom mb-3">
                <div class="card-body p-3">
                    <p class="fw-semibold mb-2 text-dark">
                        <span class="step-badge">1</span> Pilih Kantin
                    </p>
                    <select id="vendorSelect" class="form-select vendor-select w-100">
                        <option value="">-- Pilih Kantin / Vendor --</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->idvendor }}">🏪 {{ $vendor->nama_vendor }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Step 2: Pilih Menu --}}
            <div class="card card-custom mb-3">
                <div class="card-body p-3">
                    <p class="fw-semibold mb-3 text-dark">
                        <span class="step-badge">2</span> Pilih Menu
                    </p>

                    <div id="menuEmpty">
                        <i class="bi bi-arrow-up-circle fs-2 d-block mb-2"></i>
                        Pilih kantin terlebih dahulu
                    </div>

                    <div id="menuLoading" class="d-none">
                        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                        Memuat menu...
                    </div>

                    <div id="menuGrid" class="row g-2 d-none"></div>
                </div>
            </div>

            {{-- Catatan --}}
            <div id="catatanSection" class="card card-custom d-none">
                <div class="card-body p-3">
                    <label class="fw-semibold mb-2 d-block">
                        <i class="bi bi-pencil-square me-1 text-primary"></i> Catatan (opsional)
                    </label>
                    <textarea id="globalCatatan" class="form-control" rows="2"
                        placeholder="Contoh: tidak pedas, tanpa bawang, dll..."
                        style="border-radius:10px; border:2px solid #e2e8f0;"></textarea>
                </div>
            </div>

        </div>

        {{-- KANAN: Keranjang --}}
        <div class="col-lg-4">
            <div class="card card-custom" style="position: sticky; top: 16px;">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-1"><i class="bi bi-cart3 me-2 text-primary"></i>Keranjang</h6>
                    <small class="text-muted d-block mb-3">Pesanan Anda</small>

                    <div id="cartEmpty">
                        <i class="bi bi-cart-x fs-1 d-block mb-2"></i>
                        Keranjang masih kosong
                    </div>

                    <div id="cartItems"></div>

                    <div id="cartSummary" class="d-none">
                        <hr class="my-2">
                        <div class="total-box mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Total Pembayaran</span>
                                <span class="fw-bold fs-5" id="totalText">Rp 0</span>
                            </div>
                        </div>
                        <button class="btn-pay mb-2" onclick="processCheckout()">
                            <i class="bi bi-credit-card-2-front me-2"></i>Bayar Sekarang
                        </button>
                        <button class="btn btn-outline-secondary w-100 btn-sm" onclick="clearCart()">
                            <i class="bi bi-trash me-1"></i> Kosongkan Keranjang
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal Loading --}}
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow text-center p-4">
            <div class="spinner-border text-primary mx-auto mb-3"></div>
            <p class="fw-semibold mb-0">Memproses pesanan...</p>
            <p class="text-muted small">Harap tunggu sebentar</p>
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
let cart = [];
let menuList = [];
let selectedVendor = null;

const formatRp = n => 'Rp ' + Number(n).toLocaleString('id-ID');

// Pilih vendor
document.getElementById('vendorSelect').addEventListener('change', function () {
    selectedVendor = this.value;
    cart = [];
    renderCart();
    if (!selectedVendor) {
        document.getElementById('menuGrid').classList.add('d-none');
        document.getElementById('menuEmpty').classList.remove('d-none');
        document.getElementById('catatanSection').classList.add('d-none');
        return;
    }
    fetchMenu(selectedVendor);
});

function fetchMenu(idvendor) {
    document.getElementById('menuLoading').classList.remove('d-none');
    document.getElementById('menuEmpty').classList.add('d-none');
    document.getElementById('menuGrid').classList.add('d-none');

    fetch(`/customer/menu/${idvendor}`)
        .then(r => r.json())
        .then(data => {
            menuList = data;
            document.getElementById('menuLoading').classList.add('d-none');
            renderMenuGrid(data);
            document.getElementById('catatanSection').classList.remove('d-none');
        })
        .catch(() => {
            document.getElementById('menuLoading').classList.add('d-none');
            document.getElementById('menuEmpty').innerHTML = '<i class="bi bi-exclamation-circle fs-2 d-block mb-2"></i>Gagal memuat menu.';
            document.getElementById('menuEmpty').classList.remove('d-none');
        });
}

function renderMenuGrid(menus) {
    const grid = document.getElementById('menuGrid');
    if (!menus.length) {
        document.getElementById('menuEmpty').innerHTML = '<i class="bi bi-inbox fs-2 d-block mb-2"></i>Tidak ada menu tersedia.';
        document.getElementById('menuEmpty').classList.remove('d-none');
        return;
    }
    grid.innerHTML = menus.map(m => `
        <div class="col-6 col-md-4">
            <div class="menu-card h-100" onclick="addToCart(${m.idmenu})">
                ${m.path_gambar
                    ? `<img src="/storage/${m.path_gambar}" class="menu-img" alt="${m.nama_menu}">`
                    : `<div class="menu-placeholder"><i class="bi bi-egg-fried fs-2 text-muted"></i></div>`
                }
                <div class="card-body">
                    <div class="nama">${m.nama_menu}</div>
                    <div class="harga">${formatRp(m.harga)}</div>
                </div>
                <div class="add-btn"><i class="bi bi-plus-circle me-1"></i>Tambah</div>
            </div>
        </div>
    `).join('');
    grid.classList.remove('d-none');
}

function addToCart(idmenu) {
    const menu = menuList.find(m => m.idmenu === idmenu);
    if (!menu) return;
    const existing = cart.find(c => c.idmenu === idmenu);
    if (existing) {
        existing.jumlah++;
    } else {
        cart.push({ idmenu: menu.idmenu, nama: menu.nama_menu, harga: menu.harga, jumlah: 1 });
    }
    renderCart();
}

function changeQty(idmenu, delta) {
    const item = cart.find(c => c.idmenu === idmenu);
    if (!item) return;
    item.jumlah += delta;
    if (item.jumlah <= 0) cart = cart.filter(c => c.idmenu !== idmenu);
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cartItems');
    const summary   = document.getElementById('cartSummary');
    const empty     = document.getElementById('cartEmpty');

    if (!cart.length) {
        container.innerHTML = '';
        summary.classList.add('d-none');
        empty.classList.remove('d-none');
        return;
    }

    empty.classList.add('d-none');
    summary.classList.remove('d-none');

    let total = 0;
    container.innerHTML = cart.map(item => {
        const sub = item.harga * item.jumlah;
        total += sub;
        return `
        <div class="cart-item">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <span class="fw-semibold" style="font-size:.88rem;">${item.nama}</span>
                <span class="text-primary fw-bold" style="font-size:.88rem;">${formatRp(sub)}</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary qty-btn" onclick="changeQty(${item.idmenu}, -1)">−</button>
                <span class="fw-bold">${item.jumlah}</span>
                <button class="btn btn-outline-primary qty-btn" onclick="changeQty(${item.idmenu}, 1)">+</button>
                <span class="text-muted" style="font-size:.78rem;">${formatRp(item.harga)}/item</span>
            </div>
        </div>`;
    }).join('');

    document.getElementById('totalText').textContent = formatRp(total);
}

function clearCart() {
    if (!confirm('Kosongkan keranjang?')) return;
    cart = [];
    renderCart();
}

function processCheckout() {
    if (!selectedVendor) { alert('Pilih kantin terlebih dahulu!'); return; }
    if (!cart.length)     { alert('Keranjang masih kosong!'); return; }

    const catatan = document.getElementById('globalCatatan').value;
    const items   = cart.map(c => ({ idmenu: c.idmenu, jumlah: c.jumlah, catatan: catatan }));
    const modal   = new bootstrap.Modal(document.getElementById('loadingModal'));
    modal.show();

    fetch('/customer/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ idvendor: selectedVendor, items })
    })
    .then(r => r.json())
    .then(data => {
        modal.hide();
        if (data.status !== 'success') { alert('Error: ' + data.message); return; }

        window.snap.pay(data.snap_token, {
            onSuccess: () => { window.location.href = `/customer/payment/${data.order_id}`; },
            onPending: () => { window.location.href = `/customer/payment/${data.order_id}`; },
            onError:   () => { alert('Pembayaran gagal. Silakan coba lagi.'); },
            onClose:   () => { window.location.href = `/customer/payment/${data.order_id}`; }
        });
    })
    .catch(err => {
        modal.hide();
        alert('Terjadi kesalahan. Silakan coba lagi.');
        console.error(err);
    });
}
</script>
@endsection