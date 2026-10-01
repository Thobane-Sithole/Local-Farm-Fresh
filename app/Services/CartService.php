<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class CartService
{
    /** Resolve the cart for the current visitor (authenticated or guest). */
    public function resolve(Request $request): Cart
    {
        if ($user = $request->user()) {
            return Cart::firstOrCreate(['user_id' => $user->id]);
        }

        return Cart::firstOrCreate(['session_id' => $request->session()->getId()]);
    }

    /** Add $qty of a product. Increments if already in cart. */
    public function add(Cart $cart, Product $product, int $qty = 1): CartItem
    {
        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->increment('quantity', $qty);
            return $item->refresh();
        }

        return $cart->items()->create(['product_id' => $product->id, 'quantity' => $qty]);
    }

    /** Set an item's quantity; removes it if $qty ≤ 0. */
    public function update(CartItem $item, int $qty): void
    {
        $qty <= 0 ? $item->delete() : $item->update(['quantity' => $qty]);
    }

    public function remove(CartItem $item): void
    {
        $item->delete();
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }

    /**
     * Merge a guest cart into a signed-in user's cart (called after login).
     * Items already in the user cart have their quantity incremented.
     */
    public function mergeGuestCart(User $user, string $sessionId): void
    {
        $guest = Cart::where('session_id', $sessionId)->with('items')->first();

        if (! $guest || $guest->items->isEmpty()) {
            $guest?->delete();
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $user->id]);

        foreach ($guest->items as $item) {
            $this->add($userCart, $item->product, $item->quantity);
        }

        $guest->delete();
    }

    /** Total number of individual units in the cart. */
    public function itemCount(Cart $cart): int
    {
        return (int) $cart->items()->sum('quantity');
    }

    /** Grand subtotal across all items (no delivery fees). */
    public function subtotal(Cart $cart): float
    {
        return (float) $cart->items->sum(fn (CartItem $i) => $i->quantity * (float) $i->product->price);
    }
}
