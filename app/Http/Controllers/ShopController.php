<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function __invoke(Request $request): View
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->withCount(['products' => fn ($q) => $q->visible()])
            ->get();

        $query = Product::visible()
            ->with(['farmerProfile', 'primaryImage', 'category'])
            ->latest();

        $currentCategory = null;

        if ($request->filled('category')) {
            $currentCategory = $categories->firstWhere('slug', $request->category);
            if ($currentCategory) {
                $query->where('category_id', $currentCategory->id);
            }
        }

        if ($request->filled('q')) {
            $search = '%'.$request->q.'%';
            $query->where(fn ($q) => $q->where('name', 'like', $search)
                ->orWhere('short_description', 'like', $search));
        }

        $products = $query->paginate(24)->withQueryString();

        return view('shop.index', compact('products', 'categories', 'currentCategory'));
    }
}
