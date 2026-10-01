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

        $product = Product::findOrFail($data['product_id']);

        abort_unless($product->is_available && $product->removed_at === null, 422, 'This product is not available.');

        $cart = $this->cart->resolve($request);
        $this->cart->add($cart, $product, $data['quantity'] ?? 1);

        return redirect()->route('cart.index')->with('status', "{$product->name} added to your cart.");
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
