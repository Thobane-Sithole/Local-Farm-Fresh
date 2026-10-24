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
        // Verified farmers sorted by days remaining (lowest first = most urgent),
        // then unverified farmers below.
        $farmers = FarmerProfile::with('user')
            ->withCount(['products', 'orders'])
            ->orderByRaw('CASE WHEN is_verified = 1 THEN 0 ELSE 1 END ASC')
            ->orderByRaw('CASE WHEN verified_at IS NULL THEN 1 ELSE 0 END ASC')
            ->orderBy('verified_at', 'asc')
            ->paginate(20);

        $expiredCount = FarmerProfile::where('is_verified', true)
            ->whereNotNull('verified_at')
            ->where('verified_at', '<', now()->subDays(FarmerProfile::VERIFICATION_DAYS))
            ->count();

        $counts = [
            'total'      => FarmerProfile::count(),
            'verified'   => FarmerProfile::where('is_verified', true)->count(),
            'unverified' => FarmerProfile::where('is_verified', false)->count(),
            'expired'    => $expiredCount,
        ];

        return view('admin.farmers.index', compact('farmers', 'counts'));
    }

    public function verify(FarmerProfile $farmerProfile): RedirectResponse
    {
        $farmerProfile->is_verified  = true;
        $farmerProfile->verified_at  = now();
        $farmerProfile->save();

        return back()->with('status', "Farm \"{$farmerProfile->farm_name}\" verified for 30 days.");
    }

    public function unverify(FarmerProfile $farmerProfile): RedirectResponse
    {
        $farmerProfile->is_verified = false;
        $farmerProfile->verified_at = null;
        $farmerProfile->save();

        return back()->with('status', "Farm \"{$farmerProfile->farm_name}\" verification removed.");
    }
}
