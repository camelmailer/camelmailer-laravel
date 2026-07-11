<?php

declare(strict_types=1);

namespace CamelMailer\Laravel\Tests\Fixtures;

use Illuminate\Mail\Mailable;

final class TaggedWelcome extends Mailable
{
    public function build(): self
    {
        return $this->subject('Welcome!')
            ->html('<p>Welcome aboard.</p>')
            ->withSymfonyMessage(function ($message): void {
                $message->getHeaders()->addTextHeader('X-CamelMailer-Tag', 'welcome');
                $message->getHeaders()->addTextHeader('X-CamelMailer-Stream', 'onboarding');
                $message->getHeaders()->addTextHeader('X-Custom-Ref', 'abc-123');
            });
    }
}
