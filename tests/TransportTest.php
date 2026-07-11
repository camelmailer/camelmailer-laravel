<?php

declare(strict_types=1);

namespace CamelMailer\Laravel\Tests;

use CamelMailer\Exceptions\ErrorException;
use CamelMailer\Laravel\Tests\Fixtures\OrderShipped;
use CamelMailer\Laravel\Tests\Fixtures\TaggedWelcome;
use CamelMailer\Laravel\Transport\CamelMailerTransport;
use Illuminate\Support\Facades\Mail;

final class TransportTest extends TestCase
{
    public function test_the_camelmailer_mailer_uses_our_transport(): void
    {
        $transport = Mail::mailer('camelmailer')->getSymfonyTransport();

        $this->assertInstanceOf(CamelMailerTransport::class, $transport);
        $this->assertSame('camelmailer', (string) $transport);
    }

    public function test_a_mailable_is_delivered_through_the_api(): void
    {
        $this->fakeCamelMailer();
        $this->transporter->queue(['message_id' => 4711, 'recipients' => []]);

        Mail::to('ada@example.com')->send(new OrderShipped);

        $request = $this->transporter->lastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertSame('/api/v2/server/messages', $request['path']);

        $body = $request['body'];
        $this->assertNotNull($body);
        $this->assertSame(['email' => 'sender@acme.com', 'name' => 'Acme'], $body['from']);
        $this->assertSame(['ada@example.com'], $body['to']);
        $this->assertSame([['email' => 'warehouse@acme.com', 'name' => 'Warehouse']], $body['cc']);
        $this->assertSame(['support@acme.com'], $body['reply_to']);
        $this->assertSame('Your order has shipped', $body['subject']);
        $this->assertSame('<p>Your order is on its way.</p>', $body['html_body']);
    }

    public function test_attachments_are_base64_encoded(): void
    {
        $this->fakeCamelMailer();

        Mail::to('ada@example.com')->send(new OrderShipped);

        $body = $this->transporter->lastRequest()['body'];
        $this->assertNotNull($body);
        $this->assertCount(1, $body['attachments']);
        $this->assertSame('invoice.pdf', $body['attachments'][0]['name']);
        $this->assertSame('application/pdf', $body['attachments'][0]['content_type']);
        $this->assertSame('%PDF-1.4 fake', base64_decode($body['attachments'][0]['data_base64']));
    }

    public function test_tag_stream_and_custom_headers_are_mapped(): void
    {
        $this->fakeCamelMailer();

        Mail::to('ada@example.com')->send(new TaggedWelcome);

        $body = $this->transporter->lastRequest()['body'];
        $this->assertNotNull($body);
        $this->assertSame('welcome', $body['tag']);
        $this->assertSame('onboarding', $body['stream']);
        $this->assertSame('abc-123', $body['headers']['X-Custom-Ref']);
        $this->assertArrayNotHasKey('X-CamelMailer-Tag', $body['headers']);
    }

    public function test_api_errors_bubble_up_with_their_code(): void
    {
        $this->fakeCamelMailer();
        $this->transporter->queueException(
            new ErrorException('ValidationError', 'from domain is not verified', 422)
        );

        try {
            Mail::to('ada@example.com')->send(new OrderShipped);
            $this->fail('Expected an ErrorException.');
        } catch (ErrorException $exception) {
            $this->assertSame('ValidationError', $exception->code);
        }
    }
}
