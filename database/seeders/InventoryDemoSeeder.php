<?php

namespace Database\Seeders;

use App\Enums\BusinessRole;
use App\Models\Business;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductStockBalance;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InventoryDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $user = User::query()->updateOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Administrator',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            $business = Business::query()->create([
                'name' => 'Demo Umum',
                'slug' => 'demo-umum',
                'timezone' => 'Asia/Jakarta',
                'currency_code' => 'IDR',
                'is_active' => true,
            ]);

            $user->businesses()->syncWithoutDetaching([
                $business->id => ['role' => BusinessRole::Owner->value],
            ]);

            $catFood = Category::query()->create([
                'business_id' => $business->id,
                'parent_id' => null,
                'name' => 'Makanan',
                'slug' => 'makanan',
                'description' => null,
                'is_active' => true,
            ]);

            Category::query()->create([
                'business_id' => $business->id,
                'parent_id' => $catFood->id,
                'name' => 'Kemasan',
                'slug' => 'kemasan',
                'is_active' => true,
            ]);

            $unitPcs = Unit::query()->create([
                'business_id' => $business->id,
                'name' => 'Pieces',
                'code' => 'PCS',
                'is_active' => true,
            ]);

            Unit::query()->create([
                'business_id' => $business->id,
                'name' => 'Karton',
                'code' => 'CTN',
                'is_active' => true,
            ]);

            Supplier::query()->create([
                'business_id' => $business->id,
                'code' => 'SUP-001',
                'name' => 'PT Sumber Makmur',
                'phone' => '021000000',
                'is_active' => true,
            ]);

            Customer::query()->create([
                'business_id' => $business->id,
                'code' => 'CUS-001',
                'name' => 'Toko Sejahtera',
                'phone' => '081234567890',
                'is_active' => true,
            ]);

            $product = Product::query()->create([
                'business_id' => $business->id,
                'category_id' => $catFood->id,
                'unit_id' => $unitPcs->id,
                'sku' => 'SKU-001',
                'name' => 'Produk Demo',
                'cost_price' => 5000,
                'sell_price' => 7500,
                'min_stock_level' => 10,
                'is_active' => true,
            ]);

            ProductStockBalance::query()->create([
                'business_id' => $business->id,
                'product_id' => $product->id,
                'quantity' => 0,
            ]);
        });
    }
}
