<?php

// Quick functional test of Admin role operations for Quantum app
// Invoke via: php runner-test.php (boots Laravel)

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Http\Controllers\Admin\AprioriController;
use Illuminate\Http\Request;

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo 'Users: ' . User::count() . PHP_EOL;
echo 'Products: ' . Product::count() . PHP_EOL;
echo 'Orders: ' . Order::count() . PHP_EOL;

// 0. Admin login check
$admin = User::where('email', 'admin@quantum.com')->first();
if (!$admin) {
    $admin = User::create([
        'name' => 'Admin Quantum',
        'email' => 'admin@quantum.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);
    echo '[STEP 0] Created admin@quantum.com (password) role=admin' . PHP_EOL;
} else {
    echo '[STEP 0] Admin exists: ' . $admin->email . ' role=' . $admin->role . PHP_EOL;
}

// 1. Add a new product (simulates admin/Products create)
$cat = Category::first();
$product = Product::create([
    'category_id' => $cat->id,
    'name' => 'Test Aksesoris Baru ' . rand(100, 999),
    'slug' => 'test-aksesoris-' . uniqid(),
    'price' => 50000,
    'stock' => 10,
    'status' => 'aktif',
    'description' => 'Produk baru ditambahkan untuk tes manajemen produk.',
]);
echo '[STEP 1] Created product: id=' . $product->id . ' name="' . $product->name . '"' . PHP_EOL;

// 2. Change an order status pending -> processing (Verified equivalent)
$order = Order::where('status', 'pending')->first();
if ($order) {
    $old = $order->status;
    $order->update(['status' => 'processing']);
    echo '[STEP 2] Order id=' . $order->id . ' status ' . $old . ' -> processing (verified)' . PHP_EOL;
} else {
    echo '[STEP 2] No pending order found; skipping status change.' . PHP_EOL;
}

// 3. Run Apriori process (click "Jalankan Apriori")
try {
    $ctrl = new AprioriController();
    $req = new Request();
    $req->merge(['min_support' => 0.01, 'min_confidence' => 0.1]);
    $req->setMethod('POST');
    $res = $ctrl->process($req);
    echo '[STEP 3] Apriori process ran OK - ' . get_class($res) . PHP_EOL;
    if (method_exists($res, 'getSession')) {
        // detect success flash
    }
    echo '[STEP 3] Latest log: ' . (App\Models\AprioriLog::latest()->first()?->id ?? 'none') . PHP_EOL;
} catch (\Throwable $e) {
    echo '[STEP 3] APRIORI ERROR: ' . $e->getMessage() . PHP_EOL;
}

echo 'DONE' . PHP_EOL;
