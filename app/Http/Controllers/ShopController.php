<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $lat = session('customer_lat');
        $lng = session('customer_lng');
        $hasLocation = $lat !== null && $lng !== null;

        $query = Product::visible()
            ->with(['farmerProfile', 'primaryImage', 'category'])
            ->latest();

        // Filter to farms within 5 km when customer location is known
        if ($hasLocation) {
            $haversine = '(6371 * acos(LEAST(1.0, cos(radians(?)) * cos(radians(farmer_profiles.latitude)) * cos(radians(farmer_profiles.longitude) - radians(?)) + sin(radians(?)) * sin(radians(farmer_profiles.latitude)))))';

            $query->join('farmer_profiles', 'products.farmer_profile_id', '=', 'farmer_profiles.id')
                  ->whereNotNull('farmer_profiles.latitude')
                  ->whereNotNull('farmer_profiles.longitude')
                  ->whereRaw("{$haversine} <= 5", [$lat, $lng, $lat])
                  ->select('products.*');
        }

        $currentCategory = null;

        if ($request->filled('category')) {
            $currentCategory = $categories->firstWhere('slug', $request->category);
            if ($currentCategory) {
                $query->where('category_id', $currentCategory->id);
            }
        }

        if ($request->filled('q')) {
            $term = $request->q;
            if (DB::getDriverName() === 'pgsql') {
                $query->where(fn ($q) => $q
                    ->whereRaw('products.name % ?', [$term])
                    ->orWhereRaw('products.name ILIKE ?', ["%{$term}%"])
                    ->orWhereRaw('products.short_description ILIKE ?', ["%{$term}%"])
                );
            } else {
                $search = '%'.$term.'%';
                $query->where(fn ($q) => $q->where('products.name', 'like', $search)
                    ->orWhere('products.short_description', 'like', $search));
            }
        }

        $products = $query->paginate(24)->withQueryString();

        return view('shop.index', compact('products', 'categories', 'currentCategory', 'hasLocation'));
    }
}
