# Arsitektur aplikasi — Inventory (Laravel 10)

Dokumen ini menjelaskan **struktur domain**, **aliran data stok**, dan **titik ekstensi** untuk proyek `inventory-app`.

## Ringkasan stack

- **Framework:** Laravel 10  
- **Database:** MySQL (disarankan; sesuai `DB_*` di `.env`)  
- **UI:** Blade + Tailwind CSS + Vite (Laravel Breeze)  
- **Auth:** session Breeze, middleware `verified` pada area aplikasi

---

## Multi-bisnis (tenant)

### Konsep

Setiap baris data operasional milik satu **`business_id`**. Pengguna (`users`) dihubungkan ke banyak bisnis lewat pivot **`business_user`** (kolom `role`: `owner`, `admin`, `staff`).

Bisnis yang sedang dipakai disimpan di **session**: `current_business_id`.

### Komponen utama

| Komponen | Peran |
|----------|--------|
| `App\Http\Middleware\EnsureBusinessContext` | Validasi user punya akses ke bisnis di session; mengisi `BusinessContext` dan `view()->share('currentBusiness')`; membersihkan context di `terminate()`. |
| `App\Support\BusinessContext` | Menyimpan ID & model bisnis aktif selama request. |
| `App\Models\Scopes\BusinessScope` | Global scope: membatasi query ke `business_id` bisnis aktif. |
| `App\Models\Concerns\BelongsToBusiness` | Trait untuk model yang otomatis memakai scope di atas. |

### Rute

- **Tanpa** middleware `business`: pemilihan/ switch bisnis (`business.select`, `business.switch`).  
- **Dengan** middleware `business`: dashboard, CRUD, stok, laporan.

**Catatan:** User baru **tidak** otomatis mendapat bisnis; di production perlu alur undangan / onboarding yang mengisi `business_user`.

---

## Domain data (tabel inti)

### Referensi per bisnis

- **`businesses`** — profil bisnis (`slug`, `timezone`, `currency_code`, `is_active`).  
- **`categories`** — kategori; `parent_id` opsional (pohon).  
- **`units`** — satuan (`code` unik per bisnis).  
- **`suppliers`**, **`customers`** — mitra.  
- **`products`** — SKU unik per bisnis, taut ke kategori & satuan, harga pokok/jual, `min_stock_level`.

### Stok

- **`product_stock_balances`** — saldo **cache** per `(business_id, product_id)`. Diperbarui hanya saat **posting** dokumen stok (bukan saat simpan draft).  
- **`stock_transactions`** — header dokumen: `type` (`in` | `out` | `adjustment`), `status` (`draft` | `posted` | `voided`), tanggal, relasi opsional supplier/customer, `document_number`.  
- **`stock_transaction_lines`** — baris: `product_id`, `qty`, `unit_cost` (relevan untuk **masuk**). Kolom `business_id` disalin untuk query & scope yang efisien.  
- **`stock_ledger_entries`** — jejak **audit** per baris setelah posting: `change_qty`, `quantity_after`, `recorded_at`. Tidak menggantikan baris dokumen; untuk laporan gerakan dan kepatuhan.

### Penomoran dokumen

- **`business_sequences`** — counter per `(business_id, sequence_key, year)` dengan kunci unik.  
- **`App\Services\BusinessSequenceService`** — `incrementWithinTransaction()` memakai `lockForUpdate` **di dalam transaksi** pembuatan draft agar aman saat konkuren.

---

## Aliran stok (state machine)

1. **Draft** — user membuat/mengubah/menghapus dokumen dan baris. Perubahan **tidak** mengubah saldo maupun ledger.  
2. **Posting** — `App\Services\StockPostingService::post()` dalam **satu transaksi DB**:  
   - kunci dokumen + baris;  
   - kunci/`firstOrCreate` saldo per produk (urutan produk disortir untuk mengurangi deadlock);  
   - untuk **keluar**: qty baris positif diubah jadi perubahan negatif; stok tidak boleh di bawah nol;  
   - untuk **penyesuaian**: `quantity` pada baris boleh negatif atau positif;  
   - tulis `stock_ledger_entries`; update `product_stock_balances`; set status **posted**, `posted_at`, `posted_by`.

### Keputusan produk

- **Tidak** ada pembatalan posting otomatis di kode ini. Koreksi dilakukan lewat **dokumen penyesuaian** atau dokumen lawan — agar jejak akuntansi/stok tetap jelas.  
- **Void** status ada di enum; alur UI saat ini fokus draft/posted. Void bisa diaktifkan nanti dengan aturan tersendiri.  
- **HPP rata-rata bergerak** belum diimplementasikan; `unit_cost` pada baris masuk disimpan untuk laporan/ekstensi ke depan.

---

## Lapisan aplikasi

| Lapisan | Isi |
|---------|-----|
| **HTTP** | Controller tipis; `FormRequest` untuk validasi. |
| **Validasi** | `app/Http/Requests/*` — aturan scoped ke `business_id` (mis. `Rule::exists` + `where('business_id', …)`). |
| **Domain / operasi** | `StockPostingService`, `BusinessSequenceService`. |
| **Model** | Relasi Eloquent + cast enum (`StockTransactionType`, `StockTransactionStatus`). |
| **View** | Blade + Alpine (baris dinamis pada form stok). |

### Enum

- `App\Enums\StockTransactionType` — in, out, adjustment (+ label UI).  
- `App\Enums\StockTransactionStatus` — draft, posted, voided.  
- `App\Enums\BusinessRole` — owner, admin, staff.

---

## Laporan

| Rute | Sumber data |
|------|-------------|
| `reports.stock-on-hand` | `product_stock_balances` + join `products` — valuasi perkiraan (`qty × cost_price`, `qty × sell_price`). |
| `reports.movements` | `stock_ledger_entries` + filter tanggal/produk. |
| `reports.low-stock` | saldo vs `products.min_stock_level`. |

---

## Setup lokal singkat

```bash
cp .env.example .env
php artisan key:generate
# Set DB MySQL di .env, buat database kosong

composer install
npm install && npm run build   # atau npm run dev

php artisan migrate:fresh --seed
php artisan serve
```

**Seed demo** (`TokoAtkDemoSeeder`): skenario **Toko ATK Mandiri** — master lengkap (kategori bertingkat, satuan, supplier, pelanggan, 15+ produk), dua user (pemilik + staff), transaksi stok terposting (pembelian, dua penjualan, penyesuaian) + satu **draft** pembelian; saldo & ledger konsisten via `StockPostingService`. Login pemilik: `admin@tokoatkmandiri.test` / `password`; staff: `budi.staff@tokoatkmandiri.test` / `password`.

---

## Dependensi antar migrasi (urutan)

`businesses` → kategori, satuan, supplier, customer, produk → saldo produk → `stock_transactions` → baris → ledger → `business_user` → `business_sequences`.

Jika urutan file migrasi diubah, pastikan FK dan unique index masih konsisten.

---

## Ekstensi yang umum di production

1. **Policy** per `business_user.role` (siapa boleh posting / hapus / laporan).  
2. **Multi-gudang** — tambah `warehouse_id` pada saldo, baris dokumen, dan ledger; sesuaikan posting.  
3. **Batch / expiry** — entitas batch dan alokasi FIFO/lots.  
4. **Antrian** — ekspor laporan besar, notifikasi stok minimum.  
5. **API** (Sanctum) — layer resource/controller terpisah dengan guard yang sama + `BusinessContext` dari token/header.

---

## Peta file cepat

```
app/
  Enums/
  Http/Controllers/     # Resource + Dashboard, Report, BusinessSwitch, Stock posting
  Http/Middleware/      # EnsureBusinessContext
  Http/Requests/
  Models/Concerns/      # BelongsToBusiness
  Models/Scopes/        # BusinessScope
  Services/             # StockPosting, BusinessSequence
  Support/              # BusinessContext
database/migrations/
database/seeders/       # TokoAtkDemoSeeder
routes/web.php
docs/ARCHITECTURE.md    # dokumen ini
```

---

*Terakhir diselaraskan dengan struktur repositori pada saat dokumen dibuat. Setelah refactor besar, perbarui bagian “Peta file” dan tabel domain jika perlu.*
