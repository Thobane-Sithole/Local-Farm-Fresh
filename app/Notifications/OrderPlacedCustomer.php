<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class OrderPlacedCustomer extends Notification implements ShouldQueue
{
    use Queueable;

    /** @param Collection<Order> $orders */
    public function __construct(private readonly Collection $orders) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail', 'sms'];
    }

    public function toDatabase(object $notifiable): array
    {
        $count = $this->orders->count();
        $first = $this->orders->first();

        return [
            'type'         => 'order_placed',
            'message'      => $count === 1
                ? "Your order {$first->order_number} has been placed."
                : "{$count} orders have been placed (group: {$first->checkout_group}).",
            'order_number' => $first->order_number,
            'url'          => route('account.orders.show', $first->order_number),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $first = $this->orders->first();
        $total = $this->orders->sum(fn ($o) => (float) $o->total);

        $mail = (new MailMessage)
            ->subject("Order {$first->order_number} confirmed – Local-Farm-Fresh")
            ->greeting("Hi {$notifiable->name},")
            ->line('Thank you for your order! Here is a summary:')
            ->line('');

        foreach ($this->orders as $order) {
            $mail->line("**{$order->order_number}** — {$order->farmerProfile->farm_name}: R" . number_format((float) $order->total, 2));
        }

        $mail->line('')
             ->line('**Total: R' . number_format($total, 2) . '**')
             ->line('Payment: Cash on delivery')
             ->action('View your order', route('account.orders.show', $first->order_number))
             ->line("We'll keep you updated as the farmer prepares your order.");

        return $mail;
    }

    public function toSms(object $notifiable): string
    {
        $first = $this->orders->first();
        $total = $this->orders->sum(fn ($o) => (float) $o->total);

        return "Local-Farm-Fresh: Order {$first->order_number} placed — R"
            . number_format($total, 2) . ' total. Cash on delivery. '
            . url(route('account.orders.show', $first->order_number));
    }
}
