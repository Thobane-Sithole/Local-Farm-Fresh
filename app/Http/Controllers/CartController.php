<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    public function index(Request $request): View
    {
        $cart = $this->cart->resolve($request);
        $cart->load('items.product.farmerProfile', 'items.product.images');

        // Group by farmer for the summary panel
        $groups = $cart->items->groupBy(fn ($i) => $i->product->farmer_profile_id);

        return view('cart.index', [
            'cart'   => $cart,
            'groups' => $groups,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['sometimes', 'integer', 'min:1', 'max:999'],
        ]);

        $product = Product::with('farmerProfile')->findOrFail($data['product_id']);
        $qty = $data['quantity'] ?? 1;

        abort_unless($product->is_available && $product->removed_at === null, 422, 'This product is not available.');

        if ($product->quantity_available <= 0) {
            return back()->with('error', "{$product->name} is currently out of stock.");
        }

        if ($qty > $product->quantity_available) {
            return back()->with('error', "Only {$product->quantity_available} {$product->unit->label()} of {$product->name} available.");
        }

        $cart = $this->cart->resolve($request);
        $cart->load('items.product');

        // Single-farmer rule: block adding from a different farm
        $cartFarmerIds = $cart->items->pluck('product.farmer_profile_id')->unique()->filter()->values();
        if ($cartFarmerIds->isNotEmpty() && ! $cartFarmerIds->contains($product->farmer_profile_id)) {
            session()->flash('cart_conflict', [
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'quantity'     => $qty,
                'farmer_name'  => $product->farmerProfile->farm_name,
            ]);
            return redirect()->route('cart.index');
        }

        $this->cart->add($cart, $product, $qty);

        return redirect()->route('cart.index')->with('status', "{$product->name} added to your cart.");
    }

    public function replace(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        abort_unless($product->is_available && $product->removed_at === null, 422, 'This product is not available.');

        $cart = $this->cart->resolve($request);
        $this->cart->clear($cart);
        $this->cart->add($cart, $product, $data['quantity']);

        return redirect()->route('cart.index')->with('status', "Cart replaced. {$product->name} added.");
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:999']]);
        $this->cart->update($cartItem, $data['quantity']);

        return redirect()->route('cart.index');
    }

    public function destroy(CartItem $cartItem): RedirectResponse
    {
        $cartItem->delete();

        return redirect()->route('cart.index')->with('status', 'Item removed from cart.');
    }
}
