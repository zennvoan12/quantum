<?php
/**
 * Smoke test routes as Guest / Pembeli / Admin using Laravel HTTP kernel.
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

function hit($kernel, string $method, string $uri, array $data = [], ?User $user = null): array
{
    if ($user) {
        Auth::login($user);
    } else {
        Auth::logout();
    }

    $request = Illuminate\Http\Request::create($uri, $method, $data);
    $request->headers->set('Accept', 'text/html');
    try {
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        $kernel->terminate($request, $response);
        return [$status, $uri, $method];
    } catch (Throwable $e) {
        return [500, $uri, $method . ' ERR: ' . $e->getMessage()];
    }
}

$report = [];
$fail = 0;

// ---- GUEST ----
$guestRoutes = [
    ['GET', '/'],
    ['GET', '/produk'],
    ['GET', '/login'],
    ['GET', '/register'],
];
$product = Product::first();
if ($product) {
    $guestRoutes[] = ['GET', '/produk/' . $product->slug];
}

echo "=== GUEST ===\n";
foreach ($guestRoutes as [$m, $u]) {
    [$s, $uri, $extra] = hit($kernel, $m, $u, [], null);
    $ok = $s >= 200 && $s < 400;
    if (!$ok) $fail++;
    echo ($ok ? 'OK ' : 'FAIL ') . "$s $m $uri\n";
    $report[] = compact('s', 'm', 'uri', 'ok') + ['role' => 'guest'];
}

// Guest should be blocked from cart
[$s] = hit($kernel, 'GET', '/cart', [], null);
echo (($s === 302 || $s === 401 || $s === 403) ? 'OK ' : 'FAIL ') . "$s GET /cart (expect redirect)\n";
if (!in_array($s, [302, 401, 403], true)) $fail++;

// ---- PEMBELI ----
$pembeli = User::where('email', 'pembeli@quantum.com')->first();
echo "\n=== PEMBELI ===\n";
if (!$pembeli) {
    echo "FAIL pembeli user missing\n";
    $fail++;
} else {
    $pembeliRoutes = [
        ['GET', '/'],
        ['GET', '/produk'],
        ['GET', '/cart'],
        ['GET', '/checkout'],
        ['GET', '/transaksi-saya'],
        ['GET', '/profil/edit'],
    ];
    if ($product) {
        $pembeliRoutes[] = ['GET', '/produk/' . $product->slug];
    }
    $order = Order::where('user_id', $pembeli->id)->latest()->first();
    if ($order) {
        $pembeliRoutes[] = ['GET', '/transaksi-saya/' . $order->id];
    }
    foreach ($pembeliRoutes as [$m, $u]) {
        [$s, $uri] = hit($kernel, $m, $u, [], $pembeli);
        $ok = $s >= 200 && $s < 400;
        if (!$ok) $fail++;
        echo ($ok ? 'OK ' : 'FAIL ') . "$s $m $uri\n";
    }
    // Admin area forbidden
    [$s] = hit($kernel, 'GET', '/admin/dashboard', [], $pembeli);
    echo (($s === 302 || $s === 403) ? 'OK ' : 'FAIL ') . "$s GET /admin/dashboard as pembeli (expect deny)\n";
    if (!in_array($s, [302, 403], true)) $fail++;
}

// ---- ADMIN ----
$admin = User::where('email', 'admin@quantum.com')->first();
echo "\n=== ADMIN ===\n";
if (!$admin) {
    echo "FAIL admin user missing\n";
    $fail++;
} else {
    $adminRoutes = [
        ['GET', '/admin/dashboard'],
        ['GET', '/admin/transaksi'],
        ['GET', '/admin/orders'],
        ['GET', '/admin/products'],
        ['GET', '/admin/categories'],
        ['GET', '/admin/apriori'],
    ];
    $order = Order::latest()->first();
    if ($order) {
        $adminRoutes[] = ['GET', '/admin/orders/' . $order->id];
    }
    foreach ($adminRoutes as [$m, $u]) {
        [$s, $uri] = hit($kernel, $m, $u, [], $admin);
        $ok = $s >= 200 && $s < 400;
        if (!$ok) $fail++;
        echo ($ok ? 'OK ' : 'FAIL ') . "$s $m $uri\n";
    }
}

echo "\nTOTAL_FAIL={$fail}\n";
exit($fail > 0 ? 1 : 0);
