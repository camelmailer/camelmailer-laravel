<?php

declare(strict_types=1);

namespace CamelMailer\Laravel\Tests;

use CamelMailer\Client;
use CamelMailer\Laravel\Facades\CamelMailer;
use CamelMailer\Resources\Emails;
use CamelMailer\Resources\Templates;

final class ServiceProviderTest extends TestCase
{
    public function test_the_client_is_registered_as_a_singleton(): void
    {
        $client = $this->app->make(Client::class);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertSame($client, $this->app->make(Client::class));
        $this->assertSame($client, $this->app->make('camelmailer'));
    }

    public function test_the_facade_exposes_the_resources(): void
    {
        $this->assertInstanceOf(Emails::class, CamelMailer::emails());
        $this->assertInstanceOf(Templates::class, CamelMailer::templates());
    }

    public function test_the_facade_proxies_client_methods(): void
    {
        $this->fakeCamelMailer();
        $this->transporter->queue(['pong' => true]);

        $result = CamelMailer::ping();

        $this->assertSame('GET', $this->transporter->lastRequest()['method']);
        $this->assertSame('/api/v2/server/ping', $this->transporter->lastRequest()['path']);
        $this->assertTrue($result['pong']);
    }

    public function test_facade_sends_through_the_sdk(): void
    {
        $this->fakeCamelMailer();
        $this->transporter->queue(['message_id' => 99]);

        $result = CamelMailer::emails()->send([
            'from' => 'billing@acme.com',
            'to' => ['ada@example.com'],
            'subject' => 'Hi',
        ]);

        $this->assertSame('/api/v2/server/messages', $this->transporter->lastRequest()['path']);
        $this->assertSame(99, $result->message_id);
    }
}
