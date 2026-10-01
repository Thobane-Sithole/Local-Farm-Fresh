<?php

namespace App\Http\Controllers;

use App\Models\FarmerProfile;
use Illuminate\View\View;

class FarmerPageController extends Controller
{
    public function index(): View
    {
        $farmers = FarmerProfile::listed()
            ->withCount(['products' => fn ($q) => $q->visible()])
            ->with('user')
            ->orderBy('farm_name')
            ->paginate(20);

        return view('shop.farmers', compact('farmers'));
    }

    public function show(FarmerProfile $farmerProfile): View
    {
        abort_unless(! $farmerProfile->user->isSuspended(), 404);

        $products = $farmerProfile->products()
            ->visible()
            ->with(['primaryImage', 'category'])
            ->latest()
            ->get();

        return view('shop.farmer', compact('farmerProfile', 'products'));
    }
}
