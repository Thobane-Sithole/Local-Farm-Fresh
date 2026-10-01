<?php

namespace App\Http\Controllers;

use App\Enums\Province;
use App\Http\Requests\CheckoutRequest;
use App\Models\Address;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService  $cartService,
        private readonly OrderService $orderService,
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        $cart = $this->cartService->resolve($request);
        $cart->load('items.product.farmerProfile');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $groups    = $cart->items->groupBy(fn ($i) => $i->product->farmer_profile_id);
        $provinces = Province::options();

        // Pre-fill from the customer's default address if available
        $defaultAddress = $request->user()?->addresses()->where('is_default', true)->first();

        return view('checkout.index', compact('cart', 'groups', 'provinces', 'defaultAddress'));
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $cart = $this->cartService->resolve($request);
        $cart->load('items.product.farmerProfile');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $data = $request->validated();

        // Build a transient Address object (not saved to DB) for the order snapshot
        $address = new Address($data);
        $address->province = Province::from($data['province']);

        $orders = $this->orderService->checkout($cart, $address, $request->user());

        $group = $orders->first()->checkout_group;

        return redirect()->route('checkout.confirmed', ['group' => $group]);
    }

    public function confirmed(Request $request): View|RedirectResponse
    {
        $group = $request->query('group');

        if (! $group) {
            return redirect()->route('shop.index');
        }

        $orders = $request->user()
            ->orders()
            ->where('checkout_group', $group)
            ->with('items', 'farmerProfile')
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->route('account.orders.index');
        }

        return view('checkout.confirmed', compact('orders'));
    }
}
