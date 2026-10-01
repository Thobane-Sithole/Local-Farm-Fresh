<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewFarmer extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Review $review) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        $stars = str_repeat('★', $this->review->rating) . str_repeat('☆', 5 - $this->review->rating);

        return [
            'type'    => 'new_review',
            'message' => "{$this->review->customer->name} left a {$this->review->rating}-star review: {$stars}",
            'url'     => route('farmer.orders.show', $this->review->order->order_number),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $review   = $this->review;
        $stars    = str_repeat('★', $review->rating) . str_repeat('☆', 5 - $review->rating);
        $customer = $review->customer->name;

        $mail = (new MailMessage)
            ->subject("New {$review->rating}-star review from {$customer} – Local-Farm-Fresh")
            ->greeting("Hi {$notifiable->name},")
            ->line("{$customer} left you a review for order **{$review->order->order_number}**.")
            ->line("**Rating: {$stars} ({$review->rating}/5)**");

        if ($review->body) {
            $mail->line("*\"{$review->body}\"*");
        }

        return $mail->action('View order', route('farmer.orders.show', $review->order->order_number));
    }
}
