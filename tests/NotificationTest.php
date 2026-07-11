<?php

declare(strict_types=1);

namespace CamelMailer\Laravel\Tests;

use CamelMailer\Laravel\Tests\Fixtures\InvoicePaidNotification;
use Illuminate\Notifications\AnonymousNotifiable;

final class NotificationTest extends TestCase
{
    public function test_a_notification_is_delivered_through_the_api(): void
    {
        $this->fakeCamelMailer();
        $this->transporter->queue(['message_id' => 7, 'recipients' => []]);

        (new AnonymousNotifiable)
            ->route('mail', 'ada@example.com')
            ->notify(new InvoicePaidNotification);

        $request = $this->transporter->lastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertSame('/api/v2/server/messages', $request['path']);

        $body = $request['body'];
        $this->assertNotNull($body);
        $this->assertSame(['ada@example.com'], $body['to']);
        $this->assertSame('Invoice paid', $body['subject']);
        $this->assertStringContainsString('Your invoice has been paid', $body['html_body']);
    }
}
