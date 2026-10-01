<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductPageController extends Controller
{
    public function __invoke(Product $product): View
    {
        abort_unless($product->is_available && $product->removed_at === null, 404);
        abort_unless(! $product->farmerProfile?->user?->isSuspended(), 404);

        $product->load(['farmerProfile.user', 'images', 'category']);

        $otherProducts = Product::visible()
            ->where('farmer_profile_id', $product->farmer_profile_id)
            ->where('id', '!=', $product->id)
            ->with('primaryImage')
            ->limit(4)
            ->get();

        return view('shop.show', compact('product', 'otherProducts'));
    }
}
