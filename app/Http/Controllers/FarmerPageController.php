<?php

namespace App\Http\Controllers;

use App\Models\FarmerProfile;
use Illuminate\View\View;

class FarmerPageController extends Controller
{
    public function index(): View
    {
        $lat = session('customer_lat');
        $lng = session('customer_lng');
        $hasLocation = $lat !== null && $lng !== null;

        $query = FarmerProfile::listed()
            ->withCount(['products' => fn ($q) => $q->visible()])
            ->with('user');

        if ($hasLocation) {
            $query->nearby((float) $lat, (float) $lng, 5);
        } else {
            $query->orderBy('farm_name');
        }

        $farmers = $query->paginate(20);

        return view('shop.farmers', compact('farmers', 'hasLocation'));
    }

    public function show(FarmerProfile $farmerProfile): View
    {
        abort_unless(! $farmerProfile->user->isSuspended(), 404);

        $products = $farmerProfile->products()
            ->visible()
            ->with(['primaryImage', 'category'])
            ->latest()
            ->get();

        $reviews = $farmerProfile->reviews()
            ->with('customer')
            ->latest()
            ->limit(10)
            ->get();

        $avgRating  = $farmerProfile->averageRating();
        $reviewCount = $farmerProfile->reviews()->count();

        return view('shop.farmer', compact('farmerProfile', 'products', 'reviews', 'avgRating', 'reviewCount'));
    }
}
