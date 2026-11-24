<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(array $override = []): User
    {
        return User::create(array_merge([
            'name'      => 'Admin Toko',
            'email'     => 'admin@kelontong.test',
            'password'  => 'rahasia123',
            'role'      => User::ROLE_ADMIN,
            'is_active' => true,
        ], $override));
    }

    public function test_halaman_login_bisa_dibuka(): void
    {
        $this->get('/login')->assertOk()->assertSee('Sistem Kasir Toko Kelontong');
    }

    public function test_admin_bisa_login_dan_diarahkan_ke_dashboard_admin(): void
    {
        $user = $this->makeUser();

        $this->post('/login', ['email' => $user->email, 'password' => 'rahasia123'])
             ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_kasir_bisa_login_dan_diarahkan_ke_dashboard_kasir(): void
    {
        $kasir = $this->makeUser([
            'name' => 'Kasir Satu',
            'email' => 'kasir@kelontong.test',
            'role' => User::ROLE_KASIR,
        ]);

        $this->post('/login', ['email' => $kasir->email, 'password' => 'rahasia123'])
             ->assertRedirect(route('kasir.dashboard'));

        $this->assertAuthenticatedAs($kasir);
    }

    public function test_login_gagal_dengan_password_salah(): void
    {
        $user = $this->makeUser();

        $this->from('/login')
             ->post('/login', ['email' => $user->email, 'password' => 'salah'])
             ->assertRedirect('/login')
             ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_akun_nonaktif_tidak_bisa_login(): void
    {
        $user = $this->makeUser(['is_active' => false]);

        $this->post('/login', ['email' => $user->email, 'password' => 'rahasia123'])
             ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_kasir_dilarang_mengakses_halaman_admin(): void
    {
        $kasir = $this->makeUser(['role' => User::ROLE_KASIR]);

        $this->actingAs($kasir)
             ->get(route('admin.dashboard'))
             ->assertForbidden();
    }

    public function test_transaksi_kasir_berhasil_dan_mengurangi_stok(): void
    {
        $kasir = $this->makeUser(['role' => User::ROLE_KASIR]);
        $cat = Category::create(['name' => 'Sembako']);
        $product = Product::create([
            'code' => '899123456',
            'name' => 'Minyak Goreng 1L',
            'category_id' => $cat->id,
            'buy_price' => 15000,
            'sell_price' => 18000,
            'stock' => 10,
            'min_stock' => 2,
            'unit' => 'pouch',
            'expired_date' => Carbon::now()->addMonths(6),
        ]);

        $payload = [
            'items' => [
                ['id' => $product->id, 'quantity' => 2],
            ],
            'cash_amount' => 50000,
            'notes' => 'Pembeli langganan',
        ];

        $response = $this->actingAs($kasir)
             ->postJson(route('kasir.api.transaction.store'), $payload);

        $response->assertOk()->assertJson(['success' => true]);

        // Stok harus berkurang dari 10 jadi 8
        $this->assertEquals(8, $product->fresh()->stock);

        // Transaksi & riwayat pergerakan stok harus tercatat
        $this->assertDatabaseHas('transactions', [
            'total_items' => 2,
            'total_amount' => 36000,
            'cash_amount' => 50000,
            'change_amount' => 14000,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 2,
            'current_stock' => 8,
        ]);
    }

    public function test_barang_kedaluwarsa_dilarang_dijual(): void
    {
        $kasir = $this->makeUser(['role' => User::ROLE_KASIR]);
        $cat = Category::create(['name' => 'Makanan']);
        $expiredProduct = Product::create([
            'code' => 'EXP123456',
            'name' => 'Susu Kadaluarsa',
            'category_id' => $cat->id,
            'buy_price' => 5000,
            'sell_price' => 7000,
            'stock' => 5,
            'min_stock' => 1,
            'unit' => 'botol',
            'expired_date' => Carbon::yesterday(),
        ]);

        $payload = [
            'items' => [
                ['id' => $expiredProduct->id, 'quantity' => 1],
            ],
            'cash_amount' => 10000,
        ];

        $response = $this->actingAs($kasir)
             ->postJson(route('kasir.api.transaction.store'), $payload);

        $response->assertStatus(422);
        // Stok tidak boleh berkurang
        $this->assertEquals(5, $expiredProduct->fresh()->stock);
    }
}
