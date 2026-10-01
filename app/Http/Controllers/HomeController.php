<?php

namespace App\Http\Controllers;

use App\Models\FarmerProfile;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        // Cached briefly: the homepage is the busiest page and this list changes rarely.
        $farms = Cache::remember('home.farms', now()->addMinutes(10), fn () => FarmerProfile::query()
            ->listed()
            ->withCount(['products' => fn ($q) => $q->visible()])
            ->latest()
            ->limit(5)
            ->get());

        return view('home', ['farms' => $farms]);
    }
}
