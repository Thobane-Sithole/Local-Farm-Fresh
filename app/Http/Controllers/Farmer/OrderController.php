<?php

namespace App\Http\Controllers\Farmer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders) {}

    public function index(Request $request): View
    {
        $profile = $request->user()->farmerProfile;

        $query = Order::where('farmer_profile_id', $profile->id)
            ->with('customer')
            ->latest('placed_at');

        if ($request->filled('status') && $status = OrderStatus::tryFrom($request->status)) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(20)->withQueryString();

        $counts = [
            'all'     => Order::where('farmer_profile_id', $profile->id)->count(),
            'open'    => Order::where('farmer_profile_id', $profile->id)->whereNotIn('status', [OrderStatus::Delivered->value, OrderStatus::Cancelled->value])->count(),
            'pending' => Order::where('farmer_profile_id', $profile->id)->where('status', OrderStatus::Pending->value)->count(),
        ];

        return view('farmer.orders.index', compact('orders', 'counts'));
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->farmer_profile_id === $request->user()->farmerProfile?->id, 403);

        $order->load('items', 'customer', 'statusEvents');

        $transitions = $order->status->allowedTransitions();

        return view('farmer.orders.show', compact('order', 'transitions'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->farmer_profile_id === $request->user()->farmerProfile?->id, 403);

        $data = $request->validate([
            'status' => ['required', 'string'],
            'note'   => ['nullable', 'string', 'max:500'],
        ]);

        $next = OrderStatus::tryFrom($data['status']);

        if (! $next || ! $order->status->canTransitionTo($next)) {
            return back()->with('error', 'That status change is not permitted.');
        }

        $this->orders->advanceStatus($order, $next, $request->user(), $data['note'] ?? null);

        return back()->with('status', "Order {$order->order_number} updated to {$next->label()}.");
    }
}
