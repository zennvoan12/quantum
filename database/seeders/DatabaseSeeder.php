<?php

namespace Database\Seeders;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Users
        \App\Models\User::create([
            'name' => 'Admin Quantum',
            'email' => 'admin@quantum.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
        ]);

        \App\Models\User::create([
            'name' => 'Pembeli Demo',
            'email' => 'pembeli@quantum.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'pelanggan',
        ]);

        $category = \App\Models\Category::create([
            'name' => 'Aksesoris',
            'slug' => 'aksesoris'
        ]);

        $catalog = [
            'Tempered Glass' => 25000,
            'Softcase' => 35000,
            'Charger 20W' => 85000,
            'Kabel Data USB-C' => 25000,
            'Headset Bluetooth' => 120000,
            'Splitter Audio' => 15000,
            'Powerbank 10000mAh' => 150000,
            'Memory Card 32GB' => 60000,
        ];
        $products = [];
        foreach ($catalog as $name => $price) {
            $products[$name] = Product::create([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
                'price' => $price,
                'category_id' => $category->id,
                'stock' => 50,
                'description' => 'Produk berkualitas tinggi untuk perlindungan dan kenyamanan smartphone Anda.',
            ]);
        }

        // Pola bundel disengaja untuk demo Apriori (acak murni = rule kosong = demo mati)
        $bundles = [
            [['Tempered Glass', 'Softcase'], 0.23],
            [['Charger 20W', 'Kabel Data USB-C'], 0.20],
            [['Headset Bluetooth', 'Splitter Audio'], 0.15],
        ];
        $names = array_keys($catalog);

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

            $t = new Order();
            $t->created_at = now()->subDays(rand(0, 89))->subMinutes(rand(0, 600));
            $t->invoice_no = 'INV-' . strtoupper(uniqid());
            $t->total = 0;
            $t->status = 'completed';
            $t->save();
            foreach ($picked->unique() as $n) {
                OrderItem::create([
                    'order_id' => $t->id,
                    'product_id' => $products[$n]->id,
                    'qty' => 1,
                    'price' => $products[$n]->price,
                ]);
            }
        }
    }
}