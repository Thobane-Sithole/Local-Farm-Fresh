<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FarmerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FarmerController extends Controller
{
    public function index(): View
    {
        $farmers = FarmerProfile::with('user')
            ->withCount(['products', 'orders'])
            ->latest()
            ->paginate(20);

        $counts = [
            'total'      => FarmerProfile::count(),
            'verified'   => FarmerProfile::where('is_verified', true)->count(),
            'unverified' => FarmerProfile::where('is_verified', false)->count(),
        ];

        return view('admin.farmers.index', compact('farmers', 'counts'));
    }

    public function verify(FarmerProfile $farmerProfile): RedirectResponse
    {
        $farmerProfile->is_verified = true;
        $farmerProfile->save();

        return back()->with('status', "Farm \"{$farmerProfile->farm_name}\" has been verified.");
    }

    public function unverify(FarmerProfile $farmerProfile): RedirectResponse
    {
        $farmerProfile->is_verified = false;
        $farmerProfile->save();

        return back()->with('status', "Farm \"{$farmerProfile->farm_name}\" verification removed.");
    }
}
