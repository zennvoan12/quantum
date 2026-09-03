<?php

namespace App\Http\Controllers;

use App\Models\AprioriLog;
use App\Models\Product;

class ProductDetailController extends Controller
{
    public function show(Product $product)
    {
        $product->load('category');

        // Rekomendasi dari aturan Apriori terbaru: produk ini sebagai antecedent (product_id_a)
        $latestLog = AprioriLog::latest()->first();
        $topRules = $latestLog
            ? $latestLog->rules()
                ->strong()
                ->with('productA', 'productB.category')
                ->where('product_id_a', $product->id)
                ->orderByDesc('confidence')
                ->take(4)
                ->get()
            : collect();

        return view('products.show', compact('product', 'topRules'));
    }
}
