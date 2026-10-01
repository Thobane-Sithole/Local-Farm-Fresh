<?php

namespace App\Notifications;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Order       $order,
        private readonly OrderStatus $newStatus,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'         => 'order_status_changed',
            'message'      => "Order {$this->order->order_number} is now: {$this->newStatus->label()}.",
            'order_number' => $this->order->order_number,
            'status'       => $this->newStatus->value,
            'url'          => route('account.orders.show', $this->order->order_number),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order  = $this->order;
        $status = $this->newStatus->label();

        $body = match ($this->newStatus) {
            OrderStatus::Confirmed        => 'Your order has been confirmed by the farmer and will be prepared soon.',
            OrderStatus::Preparing        => 'The farmer is now preparing your order.',
            OrderStatus::ReadyForDelivery => 'Your order is packed and ready for delivery.',
            OrderStatus::OutForDelivery   => 'Your order is on its way! Please have your payment ready.',
            OrderStatus::Delivered        => 'Your order has been delivered. Enjoy your fresh produce!',
            OrderStatus::Cancelled        => 'Unfortunately your order has been cancelled.',
            default                       => 'Your order status has been updated.',
        };

        return (new MailMessage)
            ->subject("Order {$order->order_number}: {$status} – Local-Farm-Fresh")
            ->greeting("Hi {$notifiable->name},")
            ->line("**{$order->order_number}** — {$order->farmerProfile->farm_name}")
            ->line("Status: **{$status}**")
            ->line($body)
            ->action('Track your order', route('account.orders.show', $order->order_number));
    }
}
