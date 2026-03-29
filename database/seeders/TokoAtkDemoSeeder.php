<?php

namespace Database\Seeders;

use App\Enums\BusinessRole;
use App\Enums\StockTransactionStatus;
use App\Enums\StockTransactionType;
use App\Models\Business;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductStockBalance;
use App\Models\StockTransaction;
use App\Models\StockTransactionLine;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Services\BusinessSequenceService;
use App\Services\StockPostingService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Data real-case: Toko ATK — mengisi semua tabel domain inventory + contoh transaksi posting.
 */
class TokoAtkDemoSeeder extends Seeder
{
    public function run(): void
    {
        $sequence = app(BusinessSequenceService::class);
        $posting = app(StockPostingService::class);

        [$owner, $staff, $business, $supplierIds, $customerIds, $productIds] = DB::transaction(function () {
            return $this->seedMasterData();
        });

        Auth::login($owner);

        DB::transaction(function () use ($owner, $business, $supplierIds, $customerIds, $productIds, $sequence, $posting) {
            $y = (int) now()->format('Y');
            $baseDate = Carbon::now()->subDays(14);

            // 1) Pembelian stok awal (posted)
            $in1 = $this->createStockDocument(
                $business,
                $owner,
                $sequence,
                StockTransactionType::In,
                $supplierIds['sarana'],
                null,
                $baseDate->copy()->addDay(),
                'Pembelian ATK rutin — PT Sarana Pustaka Indonesia.',
                [
                    ['product' => 'PEN-BB-HITAM-STD', 'qty' => 240, 'unit_cost' => 1650],
                    ['product' => 'HVS-A4-80-RIM', 'qty' => 20, 'unit_cost' => 172000],
                    ['product' => 'BUKU-BB-38', 'qty' => 100, 'unit_cost' => 2800],
                    ['product' => 'PEN-2B-12-PACK', 'qty' => 40, 'unit_cost' => 11000],
                    ['product' => 'PENG-30', 'qty' => 60, 'unit_cost' => 1800],
                    ['product' => 'PENGHAPUS-JOYKO', 'qty' => 250, 'unit_cost' => 450],
                    ['product' => 'SPI-WB-HIT', 'qty' => 48, 'unit_cost' => 6500],
                    ['product' => 'STP-KENKO-HD10', 'qty' => 12, 'unit_cost' => 22000],
                    ['product' => 'STP-ISI-10', 'qty' => 30, 'unit_cost' => 3500],
                    ['product' => 'KLIP-32', 'qty' => 20, 'unit_cost' => 8500],
                    ['product' => 'MAP-FOLIO', 'qty' => 200, 'unit_cost' => 1100],
                    ['product' => 'BAT-AA-4', 'qty' => 36, 'unit_cost' => 12500],
                    ['product' => 'STICKY-33', 'qty' => 72, 'unit_cost' => 4500],
                    ['product' => 'LEM-UHU', 'qty' => 40, 'unit_cost' => 6500],
                ],
                $productIds
            );
            $posting->post($in1);

            // 2) Penjualan retail (posted)
            $out1 = $this->createStockDocument(
                $business,
                $owner,
                $sequence,
                StockTransactionType::Out,
                null,
                $customerIds['warung_rina'],
                $baseDate->copy()->addDays(3),
                'Penjualan eceran warung.',
                [
                    ['product' => 'PEN-BB-HITAM-STD', 'qty' => 24],
                    ['product' => 'BUKU-BB-38', 'qty' => 12],
                ],
                $productIds
            );
            $posting->post($out1);

            // 3) Penjualan ke sekolah (posted)
            $out2 = $this->createStockDocument(
                $business,
                $owner,
                $sequence,
                StockTransactionType::Out,
                null,
                $customerIds['sd_harapan'],
                $baseDate->copy()->addDays(5),
                'Pengadaan alat tulis PKL.',
                [
                    ['product' => 'PEN-2B-12-PACK', 'qty' => 15],
                    ['product' => 'PENG-30', 'qty' => 30],
                    ['product' => 'PENGHAPUS-JOYKO', 'qty' => 40],
                ],
                $productIds
            );
            $posting->post($out2);

            // 4) Penyesuaian stock opname — koreksi kecil (posted)
            $adj = $this->createStockDocument(
                $business,
                $owner,
                $sequence,
                StockTransactionType::Adjustment,
                null,
                null,
                $baseDate->copy()->addDays(7),
                'Stock opname: selisih lem stick.',
                [
                    ['product' => 'LEM-UHU', 'qty' => -2],
                ],
                $productIds
            );
            $posting->post($adj);

            // 5) Draft pembelian (belum posting)
            $this->createStockDocument(
                $business,
                $owner,
                $sequence,
                StockTransactionType::In,
                $supplierIds['nusantara'],
                null,
                $baseDate->copy()->addDays(9),
                'Draft: menunggu konfirmasi PO kalkulator.',
                [
                    ['product' => 'KALK-MJ12', 'qty' => 6, 'unit_cost' => 78000],
                ],
                $productIds
            );
        });

        Auth::logout();

        $this->command?->info('Toko ATK demo: login owner admin@tokoatkmandiri.test / password');
        $this->command?->info('Staff: budi.staff@tokoatkmandiri.test / password');
    }

    /**
     * @return array{0: User, 1: User, 2: Business, 3: array<string, int>, 4: array<string, int>, 5: array<string, int>}
     */
    private function seedMasterData(): array
    {
        $owner = User::query()->updateOrCreate(
            ['email' => 'admin@tokoatkmandiri.test'],
            [
                'name' => 'Andi Wijaya',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $staff = User::query()->updateOrCreate(
            ['email' => 'budi.staff@tokoatkmandiri.test'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $business = Business::query()->create([
            'name' => 'Toko ATK Mandiri',
            'slug' => 'toko-atk-mandiri',
            'timezone' => 'Asia/Makassar',
            'currency_code' => 'IDR',
            'is_active' => true,
        ]);

        $owner->businesses()->syncWithoutDetaching([
            $business->id => ['role' => BusinessRole::Owner->value],
        ]);
        $staff->businesses()->syncWithoutDetaching([
            $business->id => ['role' => BusinessRole::Staff->value],
        ]);

        $catTulis = Category::query()->create([
            'business_id' => $business->id,
            'parent_id' => null,
            'name' => 'Alat tulis',
            'slug' => 'alat-tulis',
            'description' => 'Pulpen, pensil, spidol, penghapus.',
            'is_active' => true,
        ]);
        $catPulpen = Category::query()->create([
            'business_id' => $business->id,
            'parent_id' => $catTulis->id,
            'name' => 'Pulpen & pensil',
            'slug' => 'pulpen-pensil',
            'is_active' => true,
        ]);
        $catSpidol = Category::query()->create([
            'business_id' => $business->id,
            'parent_id' => $catTulis->id,
            'name' => 'Spidol & whiteboard',
            'slug' => 'spidol-whiteboard',
            'is_active' => true,
        ]);

        $catKertas = Category::query()->create([
            'business_id' => $business->id,
            'parent_id' => null,
            'name' => 'Kertas & buku',
            'slug' => 'kertas-buku',
            'is_active' => true,
        ]);
        $catHvs = Category::query()->create([
            'business_id' => $business->id,
            'parent_id' => $catKertas->id,
            'name' => 'Kertas HVS',
            'slug' => 'kertas-hvs',
            'is_active' => true,
        ]);
        $catBuku = Category::query()->create([
            'business_id' => $business->id,
            'parent_id' => $catKertas->id,
            'name' => 'Buku & nota',
            'slug' => 'buku-nota',
            'is_active' => true,
        ]);

        $catKantor = Category::query()->create([
            'business_id' => $business->id,
            'parent_id' => null,
            'name' => 'Perlengkapan kantor',
            'slug' => 'perlengkapan-kantor',
            'is_active' => true,
        ]);
        $catStapler = Category::query()->create([
            'business_id' => $business->id,
            'parent_id' => $catKantor->id,
            'name' => 'Stapler & klip',
            'slug' => 'stapler-klip',
            'is_active' => true,
        ]);

        $catLem = Category::query()->create([
            'business_id' => $business->id,
            'parent_id' => $catKantor->id,
            'name' => 'Lem & lakban',
            'slug' => 'lem-lakban',
            'is_active' => true,
        ]);

        $catElektronik = Category::query()->create([
            'business_id' => $business->id,
            'parent_id' => null,
            'name' => 'Elektronik ATK',
            'slug' => 'elektronik-atk',
            'description' => 'Kalkulator, baterai.',
            'is_active' => true,
        ]);

        $uPcs = Unit::query()->create(['business_id' => $business->id, 'name' => 'Pieces', 'code' => 'PCS', 'is_active' => true]);
        $uRim = Unit::query()->create(['business_id' => $business->id, 'name' => 'Rim', 'code' => 'RIM', 'is_active' => true]);
        $uPack = Unit::query()->create(['business_id' => $business->id, 'name' => 'Pack', 'code' => 'PACK', 'is_active' => true]);
        Unit::query()->create(['business_id' => $business->id, 'name' => 'Box', 'code' => 'BOX', 'is_active' => true]);

        $supplierSarana = Supplier::query()->create([
            'business_id' => $business->id,
            'code' => 'SUP-SPI',
            'name' => 'PT Sarana Pustaka Indonesia',
            'phone' => '021-3890123',
            'email' => 'sales@sarana-pustaka.test',
            'address' => 'Jl. Percetakan Negara No. 88, Jakarta',
            'notes' => 'Vendor utama kertas & buku.',
            'is_active' => true,
        ]);
        $supplierNusantara = Supplier::query()->create([
            'business_id' => $business->id,
            'code' => 'SUP-NAT',
            'name' => 'CV Nusantara Stationery',
            'phone' => '031-5550199',
            'email' => 'order@nusantara-st.test',
            'address' => 'Jl. Raya Darmo Permai II No. 15, Surabaya',
            'is_active' => true,
        ]);
        $supplierPrima = Supplier::query()->create([
            'business_id' => $business->id,
            'code' => 'SUP-PTK',
            'name' => 'Toko Grosir Prima',
            'phone' => '061-4567888',
            'address' => 'Jl. Setia Budi, Medan',
            'is_active' => true,
        ]);

        $custWarung = Customer::query()->create([
            'business_id' => $business->id,
            'code' => 'CUS-WGB',
            'name' => 'Warung Bu Rina',
            'phone' => '0812-8899-001',
            'address' => 'Perumahan Alamanda Blok C/2',
            'is_active' => true,
        ]);
        $custSd = Customer::query()->create([
            'business_id' => $business->id,
            'code' => 'CUS-SD-HRP',
            'name' => 'SD Negeri Harapan 2',
            'phone' => '0274-123456',
            'email' => 'tu@sdnharapan2.sch.id',
            'is_active' => true,
        ]);
        $custKantor = Customer::query()->create([
            'business_id' => $business->id,
            'code' => 'CUS-KNV',
            'name' => 'Koperasi Karyawan Nusantara Veolia',
            'phone' => '021-998877',
            'is_active' => true,
        ]);
        $custCetak = Customer::query()->create([
            'business_id' => $business->id,
            'code' => 'CUS-PRT',
            'name' => 'Jasa Print Kilat "Rapid"',
            'phone' => '0856-2000-300',
            'is_active' => true,
        ]);

        $products = [
            ['sku' => 'PEN-BB-HITAM-STD', 'name' => 'Pulpen ballpoint hitam standar', 'cat' => $catPulpen->id, 'unit' => $uPcs->id, 'cost' => 1650, 'sell' => 3500, 'min' => 48],
            ['sku' => 'PEN-2B-12-PACK', 'name' => 'Pensil 2B isi 12 batang (pack)', 'cat' => $catPulpen->id, 'unit' => $uPack->id, 'cost' => 11000, 'sell' => 18500, 'min' => 20],
            ['sku' => 'SPI-WB-HIT', 'name' => 'Spidol whiteboard hitam', 'cat' => $catSpidol->id, 'unit' => $uPcs->id, 'cost' => 6500, 'sell' => 12000, 'min' => 24],
            ['sku' => 'PENGHAPUS-JOYKO', 'name' => 'Penghapus Joyko medium', 'cat' => $catPulpen->id, 'unit' => $uPcs->id, 'cost' => 450, 'sell' => 1200, 'min' => 100],
            ['sku' => 'HVS-A4-80-RIM', 'name' => 'Kertas HVS A4 80gr (1 rim)', 'cat' => $catHvs->id, 'unit' => $uRim->id, 'cost' => 172000, 'sell' => 210000, 'min' => 8],
            ['sku' => 'BUKU-BB-38', 'name' => 'Buku tulis Big Boss 38 lembar', 'cat' => $catBuku->id, 'unit' => $uPcs->id, 'cost' => 2800, 'sell' => 5500, 'min' => 60],
            ['sku' => 'STP-KENKO-HD10', 'name' => 'Stapler Kenko HD-10', 'cat' => $catStapler->id, 'unit' => $uPcs->id, 'cost' => 22000, 'sell' => 38000, 'min' => 6],
            ['sku' => 'STP-ISI-10', 'name' => 'Isi staples No.10 (1 box)', 'cat' => $catStapler->id, 'unit' => $uPcs->id, 'cost' => 3500, 'sell' => 6500, 'min' => 20],
            ['sku' => 'KLIP-32', 'name' => 'Klip kertas 32 mm (per box)', 'cat' => $catStapler->id, 'unit' => $uPcs->id, 'cost' => 8500, 'sell' => 14500, 'min' => 10],
            ['sku' => 'MAP-FOLIO', 'name' => 'Map plastik folio bening', 'cat' => $catKantor->id, 'unit' => $uPcs->id, 'cost' => 1100, 'sell' => 2500, 'min' => 50],
            ['sku' => 'KALK-MJ12', 'name' => 'Kalkulator Casio MJ-12D', 'cat' => $catElektronik->id, 'unit' => $uPcs->id, 'cost' => 78000, 'sell' => 115000, 'min' => 4],
            ['sku' => 'BAT-AA-4', 'name' => 'Baterai alkaline AA 4 pcs', 'cat' => $catElektronik->id, 'unit' => $uPcs->id, 'cost' => 12500, 'sell' => 22000, 'min' => 24],
            ['sku' => 'STICKY-33', 'name' => 'Sticky notes 3x3 kuning', 'cat' => $catKantor->id, 'unit' => $uPcs->id, 'cost' => 4500, 'sell' => 8500, 'min' => 36],
            ['sku' => 'PENG-30', 'name' => 'Penggaris plastik 30 cm', 'cat' => $catKantor->id, 'unit' => $uPcs->id, 'cost' => 1800, 'sell' => 4500, 'min' => 24],
            ['sku' => 'LEM-UHU', 'name' => 'Lem stik UHU 8.2g', 'cat' => $catLem->id, 'unit' => $uPcs->id, 'cost' => 6500, 'sell' => 12000, 'min' => 30],
        ];

        $productIds = [];
        foreach ($products as $row) {
            $p = Product::query()->create([
                'business_id' => $business->id,
                'category_id' => $row['cat'],
                'unit_id' => $row['unit'],
                'sku' => $row['sku'],
                'name' => $row['name'],
                'cost_price' => $row['cost'],
                'sell_price' => $row['sell'],
                'min_stock_level' => $row['min'],
                'is_active' => true,
            ]);
            ProductStockBalance::query()->create([
                'business_id' => $business->id,
                'product_id' => $p->id,
                'quantity' => 0,
            ]);
            $productIds[$row['sku']] = $p->id;
        }

        $supplierIds = [
            'sarana' => $supplierSarana->id,
            'nusantara' => $supplierNusantara->id,
            'prima' => $supplierPrima->id,
        ];

        $customerIds = [
            'warung_rina' => $custWarung->id,
            'sd_harapan' => $custSd->id,
            'koperasi' => $custKantor->id,
            'rapid' => $custCetak->id,
        ];

        return [$owner, $staff, $business, $supplierIds, $customerIds, $productIds];
    }

    /**
     * @param  array<int, array{product: string, qty: float|int, unit_cost?: float|int}>  $lines
     */
    private function createStockDocument(
        Business $business,
        User $user,
        BusinessSequenceService $sequence,
        StockTransactionType $type,
        ?int $supplierId,
        ?int $customerId,
        Carbon $occurredOn,
        string $notes,
        array $lines,
        array $productIdsBySku
    ): StockTransaction {
        return DB::transaction(function () use ($business, $user, $sequence, $type, $supplierId, $customerId, $occurredOn, $notes, $lines, $productIdsBySku) {
            $year = (int) $occurredOn->format('Y');
            $next = $sequence->incrementWithinTransaction(
                $business->id,
                BusinessSequenceService::KEY_STOCK_DOCUMENT,
                $year
            );
            $documentNumber = sprintf('STK-%d-%06d', $year, $next);

            $tx = StockTransaction::query()->create([
                'business_id' => $business->id,
                'document_number' => $documentNumber,
                'type' => $type,
                'status' => StockTransactionStatus::Draft,
                'supplier_id' => $type === StockTransactionType::In ? $supplierId : null,
                'customer_id' => $type === StockTransactionType::Out ? $customerId : null,
                'occurred_on' => $occurredOn->toDateString(),
                'notes' => $notes,
                'created_by' => $user->id,
            ]);

            foreach ($lines as $i => $line) {
                StockTransactionLine::query()->create([
                    'business_id' => $business->id,
                    'stock_transaction_id' => $tx->id,
                    'product_id' => $productIdsBySku[$line['product']],
                    'quantity' => $line['qty'],
                    'unit_cost' => $type === StockTransactionType::In
                        ? ($line['unit_cost'] ?? null)
                        : null,
                    'line_no' => $i + 1,
                ]);
            }

            return $tx->fresh(['lines']);
        });
    }
}
