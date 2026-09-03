<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function toggleWishlist(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $user = Auth::user();

        $exists = $user->wishlists()->where('product_id', $productId)->exists();

        if ($exists) {
            $user->wishlists()->where('product_id', $productId)->delete();
            return response()->json(['success' => true, 'wished' => false, 'message' => 'Dihapus dari wishlist']);
        } else {
            $user->wishlists()->create(['product_id' => $productId]);
            return response()->json(['success' => true, 'wished' => true, 'message' => 'Ditambahkan ke wishlist']);
        }
    }
}
