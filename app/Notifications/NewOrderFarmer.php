<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderFarmer extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'         => 'new_order',
            'message'      => "New order {$this->order->order_number} from {$this->order->customer->name}.",
            'order_number' => $this->order->order_number,
            'url'          => route('farmer.orders.show', $this->order->order_number),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order    = $this->order;
        $subtotal = (float) $order->subtotal;

        $mail = (new MailMessage)
            ->subject("New order {$order->order_number} – Local-Farm-Fresh")
            ->greeting("Hi {$notifiable->name},")
            ->line("You have a new order from **{$order->customer->name}**.")
            ->line('');

        foreach ($order->items as $item) {
            $mail->line("- {$item->quantity}× {$item->product_name}: R" . number_format((float) $item->line_total, 2));
        }

        $mail->line('')
             ->line("**Subtotal: R" . number_format($subtotal, 2) . "**")
             ->line("Delivery fee: R" . number_format((float) $order->delivery_fee, 2))
             ->action('View order', route('farmer.orders.show', $order->order_number))
             ->line('Please confirm or contact the customer if you cannot fulfil this order.');

        return $mail;
    }
}
