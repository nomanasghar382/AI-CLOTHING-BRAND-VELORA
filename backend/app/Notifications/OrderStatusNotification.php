<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class OrderStatusNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Order $order, private readonly string $event) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $labels = [
            'order' => 'Order received',
            'payment' => 'Payment received',
            'shipped' => 'Order shipped',
            'delivered' => 'Order delivered',
            'cancelled' => 'Order cancelled',
            'refunded' => 'Order refunded',
        ];
        $subject = $labels[$this->event] ?? 'Order update';

        return (new MailMessage)
            ->subject("{$subject}: {$this->order->number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("{$subject} for order {$this->order->number}.")
            ->line("Order total: {$this->order->currency} {$this->order->grand_total}.")
            ->action('View order', rtrim((string) config('app.frontend_url', config('app.url')), '/')."/orders/{$this->order->id}");
    }
}
