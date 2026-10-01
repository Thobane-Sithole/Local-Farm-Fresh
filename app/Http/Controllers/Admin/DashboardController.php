<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $users = DB::table('users')
            ->selectRaw('count(*) filter (where role = ?) as farmers', [UserRole::Farmer->value])
            ->selectRaw('count(*) filter (where role = ?) as customers', [UserRole::Customer->value])
            ->first();

        $orders = DB::table('orders')
            ->selectRaw('count(*) as total')
            ->selectRaw('count(*) filter (where status = ?) as pending', [OrderStatus::Pending->value])
            ->selectRaw('count(*) filter (where status = ?) as completed', [OrderStatus::Delivered->value])
            ->first();

        return view('admin.dashboard', [
            'stats' => [
                'farmers'            => (int) $users->farmers,
                'customers'          => (int) $users->customers,
                'products'           => DB::table('products')->whereNull('deleted_at')->whereNull('removed_at')->count(),
                'orders'             => (int) $orders->total,
                'pending_orders'     => (int) $orders->pending,
                'completed_orders'   => (int) $orders->completed,
                'unverified_farmers' => DB::table('farmer_profiles')->where('is_verified', false)->count(),
                'removed_products'   => DB::table('products')->whereNotNull('removed_at')->whereNull('deleted_at')->count(),
            ],
        ]);
    }
}
