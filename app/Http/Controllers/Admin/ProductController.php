<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status', 'active');

        $query = Product::with(['farmerProfile.user', 'category'])
            ->orderByDesc('created_at');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status === 'removed') {
            $query->whereNotNull('removed_at');
        } else {
            $query->whereNull('removed_at');
        }

        $products = $query->paginate(25)->withQueryString();

        $counts = [
            'active'  => Product::whereNull('removed_at')->count(),
            'removed' => Product::whereNotNull('removed_at')->count(),
        ];

        return view('admin.products.index', compact('products', 'status', 'counts'));
    }

    public function remove(Product $product): RedirectResponse
    {
        $product->removed_at = now();
        $product->save();

        return back()->with('status', "\"{$product->name}\" has been removed from the marketplace.");
    }

    public function restore(Product $product): RedirectResponse
    {
        $product->removed_at = null;
        $product->save();

        return back()->with('status', "\"{$product->name}\" has been restored to the marketplace.");
    }
}
