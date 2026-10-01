<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['customer', 'farmerProfile'])
            ->orderByDesc('placed_at');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $orders = $query->paginate(25)->withQueryString();

        $counts = [
            'all'     => Order::count(),
            'pending' => Order::where('status', OrderStatus::Pending->value)->count(),
        ];

        return view('admin.orders.index', compact('orders', 'counts'));
    }

    public function show(Order $order): View
    {
        $order->load(['customer', 'farmerProfile', 'items', 'statusEvents']);

        return view('admin.orders.show', compact('order'));
    }
}
