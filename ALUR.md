# Alur Aplikasi & Sisa Pengerjaan — Bengkel Management System

---

## Keputusan Arsitektur: Blade (bukan Inertia/React)

Frontend akan dikonversi dari React/Inertia ke **Laravel Blade + Alpine.js + TailwindCSS**.

Alasan:
- Lebih ringan, tidak perlu build step untuk setiap perubahan UI
- Blade lebih familiar untuk tim bengkel yang mungkin tidak punya frontend dev
- Alpine.js cukup untuk interaktivitas (dropdown, modal, form dinamis)
- shadcn/ui diganti dengan komponen Blade custom berbasis Tailwind

Stack final:
- **Backend**: Laravel 13 + MySQL
- **Frontend**: Blade + Alpine.js + TailwindCSS
- **RBAC**: spatie/laravel-permission
- **Audit**: spatie/laravel-activitylog

---

## Alur Aplikasi Per Modul

### 1. Login & Auth
```
User buka /login
→ Input email + password
→ Redirect ke /dashboard sesuai role
→ Sidebar menu tampil sesuai permission
```

### 2. Dashboard (per role)
```
Admin/Supervisor:
  → Lihat stats: total barang, stok kritis, WO proses, antrian bayar
  → Widget stok kritis (list barang hampir habis)
  → Widget antrian pembayaran (WO menunggu_pembayaran)

Kasir:
  → Fokus: daftar WO menunggu_pembayaran
  → Tombol langsung ke halaman bayar

Mekanik:
  → Fokus: WO aktif miliknya (status: proses)
  → Tombol buat WO baru

Gudang:
  → Fokus: stok kritis + opname pending
```

### 3. Work Order — Alur Mekanik
```
Mekanik → /work-order/create
  → Cari pelanggan (autocomplete by nama/HP)
    → Jika baru: isi form pelanggan inline
  → Pilih kendaraan pelanggan (by nopol)
    → Jika baru: isi form kendaraan inline
  → Isi keluhan
  → Pilih: WO Resmi atau Estimasi dulu?

  [Estimasi]
    → Tambah item (barang + tarif pasang) → stok BELUM dikurangi
    → Tampilkan total estimasi ke pelanggan
    → Pelanggan setuju → Konfirmasi Estimasi → stok dikurangi → status: proses

  [WO Resmi langsung]
    → Tambah item satu per satu:
        - Cari barang (scan barcode / ketik nama)
        - Harga barang otomatis dari master (bisa edit)
        - Tarif pasang otomatis dari master tarif jasa (bisa edit)
        - Qty
        - Subtotal = (harga_barang + tarif_pasang) × qty (realtime)
    → Atau pilih Paket Servis (auto-isi semua item paket)
    → Total WO terupdate realtime
    → Klik "Selesai, Kirim ke Kasir" → status: menunggu_pembayaran
    → WO muncul di dashboard kasir
```

### 4. Work Order — Alur Kasir
```
Kasir → Dashboard / /work-order?status=menunggu_pembayaran
  → Pilih WO
  → Lihat detail: item, harga, tarif pasang per baris
  → Bisa edit harga/tarif (dengan audit trail)
  → Bisa tambah diskon:
      - Diskon kecil (≤ batas): kasir langsung approve
      - Diskon besar (> batas): butuh approval supervisor
  → Checklist serah terima barang per item
  → Pilih metode bayar: Tunai / Transfer / QRIS
  → Klik "Proses Pembayaran"
  → Status → lunas, semua item is_locked = true
  → Cetak struk / invoice
```

### 5. Barang Masuk (Pembelian)
```
Gudang → /pembelian/create
  → Pilih supplier
  → Isi referensi PO/nota
  → Tambah item: pilih barang, qty, harga beli
  → Submit → stok otomatis bertambah via StockService
  → Kartu stok tercatat (tipe: masuk)
```

### 6. Mutasi Stok Antar Rak
```
Gudang → /stok/mutasi
  → Pilih barang
  → Pilih dari rak → ke rak
  → Isi qty
  → Submit → stok per lokasi berubah, total stok tetap
  → Kartu stok tercatat (tipe: mutasi_keluar + mutasi_masuk)
```

### 7. Stock Opname
```
Gudang → /opname/create
  → Sistem load semua barang + stok sistem saat ini
  → Gudang isi stok fisik per item (manual / scan barcode)
  → Submit → status: draft
    → Selisih dihitung otomatis (fisik - sistem)
    → Nilai selisih = selisih × harga_beli

  Gudang → Ajukan → status: diajukan
  → Notifikasi ke Supervisor

  Supervisor → /opname/{id}
  → Review selisih
  → Setujui → stok dikoreksi via StockService (tipe: koreksi)
  → Tolak → status: ditolak, isi catatan penolakan

  Freeze: saat opname berjalan, lokasi rak yang di-freeze
  tidak bisa transaksi keluar/masuk
```

### 8. Laporan
```
/laporan/penjualan      → filter tanggal, export Excel/PDF
/laporan/pembelian      → filter tanggal + supplier, export
/laporan/stok-minimum   → list barang stok ≤ minimum
/laporan/nilai-persediaan → total nilai stok (metode Average)
/laporan/opname         → histori sesi opname
```

---

## Sisa Pengerjaan (Belum Selesai)

### FASE 1 — Setup Blade (Prioritas Utama)
| # | Task | File |
|---|---|---|
| 1 | Hapus/abaikan semua file React (.tsx) | `resources/js/Pages/*` |
| 2 | Install Alpine.js via npm | `package.json` |
| 3 | Buat layout utama Blade (sidebar + topbar) | `resources/views/layouts/app.blade.php` |
| 4 | Buat layout auth Blade | `resources/views/layouts/auth.blade.php` |
| 5 | Buat komponen Blade: alert, badge, modal, pagination | `resources/views/components/` |
| 6 | Update `app.blade.php` (root template Inertia → Blade biasa) | `resources/views/app.blade.php` |

### FASE 2 — Database & Config
| # | Task | Status |
|---|---|---|
| 7 | Buat database `bengkel` di MySQL | Manual |
| 8 | `php artisan migrate --seed` | Manual |
| 9 | Verifikasi spatie permission UUID config | ✅ Sudah |
| 10 | Verifikasi AppServiceProvider | ✅ Sudah |

### FASE 3 — Halaman Auth
| # | Task | View |
|---|---|---|
| 11 | Login page | `views/auth/login.blade.php` |
| 12 | Update AuthController redirect ke dashboard | `LoginController` |

### FASE 4 — Dashboard
| # | Task | View |
|---|---|---|
| 13 | Dashboard per role | `views/dashboard.blade.php` |

### FASE 5 — Modul Barang
| # | Task | View |
|---|---|---|
| 14 | List barang + filter + search | `views/barang/index.blade.php` |
| 15 | Form tambah/edit barang | `views/barang/form.blade.php` |
| 16 | Detail barang + kartu stok | `views/barang/show.blade.php` |

### FASE 6 — Work Order
| # | Task | View |
|---|---|---|
| 17 | List WO + filter status | `views/work-order/index.blade.php` |
| 18 | Form buat WO (cari pelanggan, kendaraan, keluhan) | `views/work-order/create.blade.php` |
| 19 | Detail WO + tambah item + edit harga realtime | `views/work-order/show.blade.php` |
| 20 | Form bayar (kasir view) | `views/work-order/bayar.blade.php` |
| 21 | Struk/invoice print-friendly | `views/work-order/invoice.blade.php` |

### FASE 7 — Pembelian
| # | Task | View |
|---|---|---|
| 22 | List pembelian | `views/pembelian/index.blade.php` |
| 23 | Form pembelian (multi-item) | `views/pembelian/create.blade.php` |
| 24 | Detail pembelian | `views/pembelian/show.blade.php` |

### FASE 8 — Pelanggan & Kendaraan
| # | Task | View |
|---|---|---|
| 25 | List pelanggan | `views/pelanggan/index.blade.php` |
| 26 | Detail pelanggan + histori kendaraan + servis | `views/pelanggan/show.blade.php` |

### FASE 9 — Supplier
| # | Task | View |
|---|---|---|
| 27 | List supplier | `views/supplier/index.blade.php` |
| 28 | Detail supplier + riwayat pembelian | `views/supplier/show.blade.php` |

### FASE 10 — Stok
| # | Task | View |
|---|---|---|
| 29 | Kartu stok per barang | `views/stok/kartu.blade.php` |
| 30 | Form mutasi stok | `views/stok/mutasi.blade.php` |

### FASE 11 — Stock Opname
| # | Task | View |
|---|---|---|
| 31 | List sesi opname | `views/opname/index.blade.php` |
| 32 | Form input opname (scan/manual) | `views/opname/create.blade.php` |
| 33 | Detail opname + selisih | `views/opname/show.blade.php` |
| 34 | Approval supervisor | (di show.blade.php) |

### FASE 12 — Master Data
| # | Task | View |
|---|---|---|
| 35 | Kategori barang (CRUD inline) | `views/master/kategori.blade.php` |
| 36 | Lokasi rak (CRUD inline) | `views/master/rak.blade.php` |
| 37 | Tarif jasa (CRUD inline) | `views/master/tarif.blade.php` |
| 38 | Paket servis (CRUD + detail) | `views/master/paket.blade.php` |

### FASE 13 — Laporan
| # | Task | View |
|---|---|---|
| 39 | Laporan penjualan | `views/laporan/penjualan.blade.php` |
| 40 | Laporan pembelian | `views/laporan/pembelian.blade.php` |
| 41 | Laporan stok minimum | `views/laporan/stok-minimum.blade.php` |
| 42 | Laporan nilai persediaan | `views/laporan/nilai-persediaan.blade.php` |
| 43 | Export Excel (maatwebsite/excel) | `LaporanController` |
| 44 | Export PDF (barryvdh/laravel-dompdf) | `LaporanController` |

### FASE 14 — User Management
| # | Task | View |
|---|---|---|
| 45 | List user + role | `views/user/index.blade.php` |
| 46 | Form tambah/edit user | (modal di index) |

### FASE 15 — Fitur Tambahan
| # | Task | Catatan |
|---|---|---|
| 47 | Barcode scan (html5-qrcode) | Alpine.js component di form WO & opname |
| 48 | Realtime antrian kasir | Polling setiap 30 detik via Alpine.js fetch |
| 49 | Alert stok minimum | Blade component di dashboard |
| 50 | Print struk thermal 58mm/80mm | CSS print media query |
| 51 | Approval diskon bertingkat | Modal konfirmasi + route supervisor |

---

## Urutan Pengerjaan Sekarang

```
FASE 1 (Setup Blade)
  → FASE 2 (Migrate DB)
    → FASE 3 (Auth)
      → FASE 4 (Dashboard)
        → FASE 5 (Barang)
          → FASE 6 (Work Order) ← inti sistem
            → FASE 7 (Pembelian)
              → FASE 8-9 (Pelanggan, Supplier)
                → FASE 10-11 (Stok, Opname)
                  → FASE 12-14 (Master, Laporan, User)
                    → FASE 15 (Fitur Tambahan)
```

---

## Catatan Konversi Inertia → Blade

Controllers yang perlu diubah:
- Ganti semua `Inertia::render('Page', [...])` → `view('page', [...])`
- Ganti `return back()->with(...)` tetap sama ✅
- Ganti `return redirect()->route(...)` tetap sama ✅
- Hapus `use Inertia\Inertia;` dari semua controller
- Update `routes/web.php` — tidak ada perubahan route ✅

File yang perlu dihapus/diabaikan:
- `resources/js/Pages/` — semua .tsx (tidak dipakai)
- `resources/js/Layouts/` — tidak dipakai
- `resources/js/lib/` — tidak dipakai
- `resources/js/components/ui/` — tidak dipakai (shadcn)

File yang tetap dipakai:
- `resources/js/app.tsx` → ganti ke `app.js` (hanya Alpine.js + Vite)
- `vite.config.js` — tetap untuk compile CSS + JS
