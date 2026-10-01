<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Events\OrderPlaced;
use App\Events\OrderStatusAdvanced;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(private readonly OrderNumberGenerator $numbers) {}

    /**
     * Convert a cart into one order per farmer.
     * All orders share a checkout_group UUID so the customer can see them together.
     *
     * @return Collection<Order>
     */
    public function checkout(Cart $cart, Address $address, User $customer): Collection
    {
        $cart->load('items.product.farmerProfile');

        $groups = $cart->items->groupBy(fn ($i) => $i->product->farmer_profile_id);

        if ($groups->isEmpty()) {
            throw new \RuntimeException('Cart is empty.');
        }

        $checkoutGroup = (string) Str::uuid();
        $orders = collect();

        DB::transaction(function () use ($cart, $groups, $address, $customer, $checkoutGroup, &$orders) {
            foreach ($groups as $items) {
                $farmer   = $items->first()->product->farmerProfile;
                $subtotal = $items->sum(fn ($i) => $i->quantity * (float) $i->product->price);
                $fee      = (float) $farmer->delivery_fee;

                $order = new Order();
                $order->forceFill([
                    'order_number'          => $this->numbers->next(),
                    'checkout_group'        => $checkoutGroup,
                    'customer_id'           => $customer->id,
                    'farmer_profile_id'     => $farmer->id,
                    'delivery_name'         => $address->recipient_name,
                    'delivery_phone'        => $address->phone,
                    'delivery_street'       => $address->street_address,
                    'delivery_suburb'       => $address->suburb,
                    'delivery_town'         => $address->town,
                    'delivery_municipality' => $address->municipality,
                    'delivery_province'     => $address->province->value,
                    'delivery_postal_code'  => $address->postal_code,
                    'delivery_notes'        => $address->delivery_notes,
                    'subtotal'              => $subtotal,
                    'delivery_fee'          => $fee,
                    'total'                 => $subtotal + $fee,
                    'payment_method'        => PaymentMethod::CashOnDelivery->value,
                    'status'                => OrderStatus::Pending->value,
                    'placed_at'             => now(),
                ]);
                $order->save();

                foreach ($items as $item) {
                    $p = $item->product;
                    $order->items()->forceCreate([
                        'product_id'   => $p->id,
                        'product_name' => $p->name,
                        'unit'         => $p->unit->value,
                        'unit_price'   => $p->price,
                        'quantity'     => $item->quantity,
                        'line_total'   => $item->quantity * (float) $p->price,
                    ]);

                    // Reduce available stock
                    $p->decrement('quantity_available', $item->quantity);
                }

                $order->statusEvents()->forceCreate([
                    'status'     => OrderStatus::Pending->value,
                    'changed_by' => $customer->id,
                    'note'       => 'Order placed',
                ]);

                $orders->push($order);
            }

            // Clear the cart after all orders are committed
            $cart->items()->delete();
        });

        // Fire event after the transaction so the DB is committed before listeners run
        OrderPlaced::dispatch($orders, $customer);

        return $orders;
    }

    /**
     * Advance an order to the given status (farmer action).
     * Throws if the transition is not permitted.
     */
    public function advanceStatus(Order $order, OrderStatus $next, User $actor, ?string $note = null): void
    {
        if (! $order->status->canTransitionTo($next)) {
            throw new \RuntimeException("Cannot move from {$order->status->label()} to {$next->label()}.");
        }

        $timestamps = [
            OrderStatus::Confirmed->value        => 'confirmed_at',
            OrderStatus::Delivered->value        => 'delivered_at',
            OrderStatus::Cancelled->value        => 'cancelled_at',
        ];

        DB::transaction(function () use ($order, $next, $actor, $note, $timestamps) {
            $fields = ['status' => $next->value];
            if (isset($timestamps[$next->value])) {
                $fields[$timestamps[$next->value]] = now();
            }
            if ($next === OrderStatus::Cancelled && $note) {
                $fields['cancellation_reason'] = $note;
            }

            $order->forceFill($fields)->save();

            $order->statusEvents()->forceCreate([
                'status'     => $next->value,
                'changed_by' => $actor->id,
                'note'       => $note,
            ]);
        });

        OrderStatusAdvanced::dispatch($order, $next, $actor);
    }
}
