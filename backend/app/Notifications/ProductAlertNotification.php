<?php

namespace App\Notifications;

use App\Models\ProductAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class ProductAlertNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly ProductAlert $alert) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $label = $this->alert->type === 'restock' ? 'Back in stock' : 'Price alert';

        return (new MailMessage)->subject("{$label}: {$this->alert->product->name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("{$label} for {$this->alert->product->name}.")
            ->action('View product', rtrim((string) config('app.frontend_url', config('app.url')), '/')."/products/{$this->alert->product->slug}");
    }
}
