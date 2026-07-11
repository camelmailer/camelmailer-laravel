<?php

declare(strict_types=1);

namespace CamelMailer\Laravel\Tests\Fixtures;

use Illuminate\Mail\Mailable;

final class OrderShipped extends Mailable
{
    public function build(): self
    {
        return $this->subject('Your order has shipped')
            ->html('<p>Your order is on its way.</p>')
            ->cc('warehouse@acme.com', 'Warehouse')
            ->replyTo('support@acme.com')
            ->attachData('%PDF-1.4 fake', 'invoice.pdf', ['mime' => 'application/pdf']);
    }
}
