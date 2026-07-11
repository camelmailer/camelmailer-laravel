<?php

declare(strict_types=1);

namespace CamelMailer\Laravel\Tests\Fixtures;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class InvoicePaidNotification extends Notification
{
    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invoice paid')
            ->line('Your invoice has been paid. Thank you!');
    }
}
