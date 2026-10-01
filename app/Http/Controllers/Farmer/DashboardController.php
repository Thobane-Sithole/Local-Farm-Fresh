<?php

namespace App\Http\Controllers\Farmer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $farmer = $request->user()->farmerProfile;

        abort_if($farmer === null, 403, 'Your farm profile is missing. Please contact support.');

        // Two aggregate queries instead of five separate counts.
        $products = DB::table('products')
            ->where('farmer_profile_id', $farmer->id)
            ->whereNull('deleted_at')
            ->selectRaw('count(*) as total')
            ->selectRaw('count(*) filter (where is_available = true and removed_at is null) as active')
            ->first();

        $orders = DB::table('orders')
            ->where('farmer_profile_id', $farmer->id)
            ->selectRaw('count(*) as total')
            ->selectRaw('count(*) filter (where status = ?) as pending', [OrderStatus::Pending->value])
            ->selectRaw('count(*) filter (where status = ?) as completed', [OrderStatus::Delivered->value])
            ->first();

        return view('farmer.dashboard', [
            'farmer' => $farmer,
            'stats' => [
                'total_products' => (int) $products->total,
                'active_products' => (int) $products->active,
                'pending_orders' => (int) $orders->pending,
                'completed_orders' => (int) $orders->completed,
                'total_orders' => (int) $orders->total,
            ],
        ]);
    }
}
