<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Pengguna
        $admin = User::updateOrCreate(
            ['email' => 'admin@kelontong.com'],
            [
                'name' => 'Pemilik Toko (Admin)',
                'password' => Hash::make('admin123'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ]
        );

        $kasir = User::updateOrCreate(
            ['email' => 'kasir@kelontong.com'],
            [
                'name' => 'Budi Kasir',
                'password' => Hash::make('kasir123'),
                'role' => User::ROLE_KASIR,
                'is_active' => true,
            ]
        );

        // 2. Kategori Barang
        $categories = [
            'Sembako' => 'Bahan pokok kebutuhan sehari-hari seperti beras, minyak, dan gula',
            'Makanan' => 'Makanan ringan, mie instan, biskuit, dan roti',
            'Minuman' => 'Minuman kemasan, susu, kopi, teh, dan air mineral',
            'Peralatan Mandi' => 'Sabun mandi, shampo, sikat gigi, dan pasta gigi',
            'Perlengkapan Rumah Tangga' => 'Deterjen, sabun cuci piring, dan pembersih lantai',
            'Obat-obatan' => 'Obat umum warung seperti obat flu, sakit kepala, dan plester',
        ];

        $categoryModels = [];
        foreach ($categories as $name => $desc) {
            $categoryModels[$name] = Category::updateOrCreate(
                ['name' => $name],
                ['description' => $desc]
            );
        }

        // 3. Data Barang Kelontong Contoh
        $today = Carbon::today();

        $products = [
            [
                'code' => '899238811101',
                'name' => 'Beras Setra Ramos 5 Kg',
                'category_id' => $categoryModels['Sembako']->id,
                'buy_price' => 68000,
                'sell_price' => 74000,
                'stock' => 25,
                'min_stock' => 5,
                'unit' => 'karung',
                'expired_date' => $today->copy()->addMonths(12),
            ],
            [
                'code' => '899100122202',
                'name' => 'Minyak Goreng Sawit 2 Liter',
                'category_id' => $categoryModels['Sembako']->id,
                'buy_price' => 31000,
                'sell_price' => 35000,
                'stock' => 18,
                'min_stock' => 5,
                'unit' => 'pouch',
                'expired_date' => $today->copy()->addMonths(8),
            ],
            [
                'code' => '899886633303',
                'name' => 'Gula Pasir Kristal Putih 1 Kg',
                'category_id' => $categoryModels['Sembako']->id,
                'buy_price' => 15500,
                'sell_price' => 17500,
                'stock' => 30,
                'min_stock' => 5,
                'unit' => 'bks',
                'expired_date' => $today->copy()->addMonths(10),
            ],
            [
                'code' => '899886620055',
                'name' => 'Indomie Mi Goreng Spesial 85g',
                'category_id' => $categoryModels['Makanan']->id,
                'buy_price' => 2900,
                'sell_price' => 3500,
                'stock' => 80,
                'min_stock' => 20,
                'unit' => 'pcs',
                'expired_date' => $today->copy()->addMonths(6),
            ],
            [
                'code' => '899600130077',
                'name' => 'Biskuit Roma Kelapa 300g',
                'category_id' => $categoryModels['Makanan']->id,
                'buy_price' => 9000,
                'sell_price' => 11000,
                'stock' => 12,
                'min_stock' => 5,
                'unit' => 'bks',
                // Peringatan 30 hari (20 hari lagi kedaluwarsa)
                'expired_date' => $today->copy()->addDays(20),
            ],
            [
                'code' => '899317510088',
                'name' => 'Susu UHT Full Cream Cokelat 1L',
                'category_id' => $categoryModels['Minuman']->id,
                'buy_price' => 17500,
                'sell_price' => 20000,
                'stock' => 8,
                'min_stock' => 3,
                'unit' => 'kotak',
                // Peringatan penting 7 hari (5 hari lagi kedaluwarsa)
                'expired_date' => $today->copy()->addDays(5),
            ],
            [
                'code' => '899277520099',
                'name' => 'Roti Tawar Kupas Lembut',
                'category_id' => $categoryModels['Makanan']->id,
                'buy_price' => 12000,
                'sell_price' => 15000,
                'stock' => 3,
                'min_stock' => 2,
                'unit' => 'bks',
                // SUDAH KEDALUWARSA (kemarin)
                'expired_date' => $today->copy()->subDays(2),
            ],
            [
                'code' => '899999910011',
                'name' => 'Sabun Mandi Batang Lifebuoy Merah',
                'category_id' => $categoryModels['Peralatan Mandi']->id,
                'buy_price' => 3800,
                'sell_price' => 4500,
                'stock' => 4, // Stok menipis (<= min_stock 5)
                'min_stock' => 5,
                'unit' => 'pcs',
                'expired_date' => $today->copy()->addMonths(18),
            ],
            [
                'code' => '899999955522',
                'name' => 'Deterjen Bubuk Rinso Anti Noda 800g',
                'category_id' => $categoryModels['Perlengkap Rumah Tangga'] ?? $categoryModels['Perlengkapan Rumah Tangga']->id,
                'buy_price' => 18000,
                'sell_price' => 21000,
                'stock' => 15,
                'min_stock' => 4,
                'unit' => 'bks',
                'expired_date' => $today->copy()->addMonths(24),
            ],
            [
                'code' => '899123456789',
                'name' => 'Paracetamol Strip 10 Tablet 500mg',
                'category_id' => $categoryModels['Obat-obatan']->id,
                'buy_price' => 4500,
                'sell_price' => 6000,
                'stock' => 2, // Stok menipis
                'min_stock' => 5,
                'unit' => 'strip',
                'expired_date' => $today->copy()->addMonths(14),
            ],
        ];

        foreach ($products as $data) {
            $product = Product::updateOrCreate(
                ['code' => $data['code']],
                $data
            );

            // Buat catatan stok masuk awal
            StockMovement::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'reference_number' => 'STOK-AWAL-'. $product->code,
                ],
                [
                    'user_id' => $admin->id,
                    'type' => StockMovement::TYPE_IN,
                    'quantity' => $product->stock,
                    'previous_stock' => 0,
                    'current_stock' => $product->stock,
                    'notes' => 'Stok awal sistem',
                ]
            );
        }
    }
}
