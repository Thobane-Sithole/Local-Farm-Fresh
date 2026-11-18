<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        session([
            'customer_lat' => (float) $data['latitude'],
            'customer_lng' => (float) $data['longitude'],
        ]);

        return redirect()->back();
    }

    public function clear(): RedirectResponse
    {
        session()->forget(['customer_lat', 'customer_lng']);

        return redirect()->back();
    }
}
