<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __invoke(Request $request): \Illuminate\View\View
    {
        $farmer = $request->user()->farmerProfile;

        $isPgsql = DB::getDriverName() === 'pgsql';
        $monthExpr = $isPgsql
            ? DB::raw("TO_CHAR(placed_at, 'YYYY-MM') as month")
            : DB::raw("strftime('%Y-%m', placed_at) as month");

        // Revenue + order count by month (last 6 months)
        $monthly = Order::forFarmer($farmer)
            ->whereIn('status', [OrderStatus::Delivered->value, OrderStatus::OutForDelivery->value, OrderStatus::ReadyForDelivery->value, OrderStatus::Confirmed->value, OrderStatus::Preparing->value])
            ->where('placed_at', '>=', now()->subMonths(6)->startOfMonth())
            ->select(
                $monthExpr,
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders'),
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top 5 products by revenue (from delivered orders)
        $topProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.farmer_profile_id', $farmer->id)
            ->whereIn('orders.status', [OrderStatus::Delivered->value])
            ->select('order_items.product_name', DB::raw('SUM(order_items.line_total) as revenue'), DB::raw('SUM(order_items.quantity) as units'))
            ->groupBy('order_items.product_name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        // Order counts by status
        $statusCounts = Order::forFarmer($farmer)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalRevenue = Order::forFarmer($farmer)
            ->where('status', OrderStatus::Delivered->value)
            ->sum('total');

        $avgRating = $farmer->averageRating();
        $reviewCount = $farmer->reviews()->count();

        return view('farmer.analytics', compact(
            'monthly', 'topProducts', 'statusCounts', 'totalRevenue', 'avgRating', 'reviewCount'
        ));
    }
}
