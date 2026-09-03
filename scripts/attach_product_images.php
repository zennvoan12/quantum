<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

$map = [
    'Tempered Glass' => 'products/luAa6T2GAlyNSa69he5eao3J5OkfjcFpIcWq2dmE.jpg',
    'Softcase' => 'products/IxcySPiCpBtaaEOzxlWE9xXyezA6o5VZ33iYbp9p.jpg',
    'Charger 20W' => 'products/SenA1VcROeMVKlDbwQvOvwWwb5WRquEB4P0o7kOI.jpg',
    'Kabel Data USB-C' => 'products/6vtqTbOmxH3SAcVSoTyNm6H231O0Vr3uPSsdUF2d.jpg',
    'Headset Bluetooth' => 'products/JcHlSnywKgUqHNcSMxfZW4zreEGWZ9VWj6EyOAej.jpg',
    'Splitter Audio' => 'products/8K2HiMRKTfaLjsZO8bwRZWYMTrNEBtH9rQJ2SzKE.jpg',
    'Powerbank 10000mAh' => 'products/Egjs0iHT0R58WYs5G7wiKJV20vqQ1FX0VnLKaIEK.jpg',
    'Memory Card 32GB' => 'products/tN3L1KfaqVLh3hbQB6BqLPztYMrJCfN0f5pWmN9N.jpg',
];

$count = Product::count();
echo "products_in_db={$count}\n";

if ($count === 0) {
    echo "NO_PRODUCTS — run migrate:fresh --seed\n";
    exit(2);
}

foreach ($map as $name => $image) {
    $path = storage_path('app/public/' . $image);
    $ok = is_file($path) ? 'file_ok' : 'FILE_MISSING';
    $n = Product::where('name', $name)->update(['image' => $image]);
    echo ($n ? 'UPD' : 'MISS') . " {$name} -> {$image} ({$ok})\n";
}

foreach (Product::orderBy('id')->get(['id', 'name', 'image']) as $p) {
    echo "#{$p->id} {$p->name} | {$p->image}\n";
}
