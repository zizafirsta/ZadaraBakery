<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Admin
        User::create([
            'name' => 'Admin Toko',
            'email' => 'admin@tokoroti.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        // 2. Buat Akun Customer Contoh
        User::create([
            'name' => 'Pelanggan Setia',
            'email' => 'customer@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
        ]);

        // 3. Buat Kategori Roti
        $categories = ['Bread', 'Cake', 'Pastry', 'Cookies'];
        foreach ($categories as $cat) {
            Category::create(['name' => $cat]);
        }
    }
}
