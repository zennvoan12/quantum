<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin Quantum',
            'email' => 'admin@quantum.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $pembeli = User::create([
            'name' => 'Pembeli Demo',
            'email' => 'pembeli@quantum.com',
            'password' => Hash::make('password'),
            'role' => 'pelanggan',
            'no_telp' => '081234567890',
            'alamat' => 'Jl. Contoh No. 10, Cianjur',
        ]);

        $category = \App\Models\Category::create([
            'name' => 'Aksesoris',
            'slug' => 'aksesoris',
        ]);

        $catalog = [
            'Tempered Glass' => ['price' => 25000, 'image' => 'products/luAa6T2GAlyNSa69he5eao3J5OkfjcFpIcWq2dmE.jpg'],
            'Softcase' => ['price' => 35000, 'image' => 'products/IxcySPiCpBtaaEOzxlWE9xXyezA6o5VZ33iYbp9p.jpg'],
            'Charger 20W' => ['price' => 85000, 'image' => 'products/SenA1VcROeMVKlDbwQvOvwWwb5WRquEB4P0o7kOI.jpg'],
            'Kabel Data USB-C' => ['price' => 25000, 'image' => 'products/6vtqTbOmxH3SAcVSoTyNm6H231O0Vr3uPSsdUF2d.jpg'],
            'Headset Bluetooth' => ['price' => 120000, 'image' => 'products/JcHlSnywKgUqHNcSMxfZW4zreEGWZ9VWj6EyOAej.jpg'],
            'Splitter Audio' => ['price' => 15000, 'image' => 'products/8K2HiMRKTfaLjsZO8bwRZWYMTrNEBtH9rQJ2SzKE.jpg'],
            'Powerbank 10000mAh' => ['price' => 150000, 'image' => 'products/Egjs0iHT0R58WYs5G7wiKJV20vqQ1FX0VnLKaIEK.jpg'],
            'Memory Card 32GB' => ['price' => 60000, 'image' => 'products/tN3L1KfaqVLh3hbQB6BqLPztYMrJCfN0f5pWmN9N.jpg'],
        ];

        $products = [];
        foreach ($catalog as $name => $meta) {
            $products[$name] = Product::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'price' => $meta['price'],
                'image' => $meta['image'],
                'category_id' => $category->id,
                'stock' => 50,
                'status' => 'aktif',
                'description' => 'Produk berkualitas tinggi untuk perlindungan dan kenyamanan smartphone Anda.',
            ]);
        }

        // Pola bundel disengaja untuk demo Apriori
        $bundles = [
            [['Tempered Glass', 'Softcase'], 0.23],
            [['Charger 20W', 'Kabel Data USB-C'], 0.20],
            [['Headset Bluetooth', 'Splitter Audio'], 0.15],
        ];
        $names = array_keys($catalog);
        $statuses = ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'];

        for ($i = 0; $i < 200; $i++) {
            $roll = mt_rand() / mt_getrandmax();
            $picked = collect();
            $acc = 0.0;
            foreach ($bundles as [$pair, $prob]) {
                $acc += $prob;
                if ($roll < $acc) {
                    $picked = collect($pair);
                    break;
                }
            }
            if ($picked->isEmpty()) {
                $picked = collect($names)->random(mt_rand(1, 3));
            }

            $status = $statuses[array_rand($statuses)];
            // Mayoritas completed agar Apriori punya data transaksi terverifikasi
            if ($i < 140) {
                $status = 'completed';
            }

            $createdAt = now()->subDays(rand(0, 89))->subMinutes(rand(0, 600));
            $total = 0;
            $uniqueItems = $picked->unique()->values();

            foreach ($uniqueItems as $n) {
                $total += $products[$n]->price;
            }

            $order = Order::create([
                'user_id' => $pembeli->id,
                'invoice_no' => 'INV-' . strtoupper(Str::random(10)),
                'total' => $total,
                'status' => $status,
                'alamat' => $pembeli->alamat,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            foreach ($uniqueItems as $n) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $products[$n]->id,
                    'qty' => 1,
                    'price' => $products[$n]->price,
                ]);
            }

            $paymentStatus = match ($status) {
                'paid', 'processing', 'shipped', 'completed' => 'paid',
                'cancelled' => 'failed',
                default => 'pending',
            };

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'transfer_bank',
                'payment_status' => $paymentStatus,
                'paid_at' => $paymentStatus === 'paid' ? $createdAt->copy()->addMinutes(rand(5, 120)) : null,
            ]);
        }

        unset($admin);
    }
}
