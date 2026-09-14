<?php

declare(strict_types=1);

namespace CamelMailer\Laravel\Tests;

use CamelMailer\Client;
use CamelMailer\Laravel\Facades\CamelMailer;
use CamelMailer\Resources\Bounces;
use CamelMailer\Resources\Campaigns;
use CamelMailer\Resources\Dmarc;
use CamelMailer\Resources\Emails;
use CamelMailer\Resources\Inbound;
use CamelMailer\Resources\Layouts;
use CamelMailer\Resources\Logs;
use CamelMailer\Resources\Stats;
use CamelMailer\Resources\Streams;
use CamelMailer\Resources\Subscribers;
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

    public function test_the_facade_exposes_every_resource(): void
    {
        // One accessor per API surface; a missing one is how this package
        // falls behind the SDK it wraps.
        $this->assertInstanceOf(Emails::class, CamelMailer::emails());
        $this->assertInstanceOf(Templates::class, CamelMailer::templates());
        $this->assertInstanceOf(Streams::class, CamelMailer::streams());
        $this->assertInstanceOf(Stats::class, CamelMailer::stats());
        $this->assertInstanceOf(Bounces::class, CamelMailer::bounces());
        $this->assertInstanceOf(Dmarc::class, CamelMailer::dmarc());
        $this->assertInstanceOf(Campaigns::class, CamelMailer::campaigns());
        $this->assertInstanceOf(Subscribers::class, CamelMailer::subscribers());
        $this->assertInstanceOf(Layouts::class, CamelMailer::layouts());
        $this->assertInstanceOf(Inbound::class, CamelMailer::inbound());
        $this->assertInstanceOf(Logs::class, CamelMailer::logs());
    }

    public function test_the_facade_covers_every_resource_the_client_has(): void
    {
        $client = new \ReflectionClass(Client::class);
        $facade = new \ReflectionClass(CamelMailer::class);

        $resources = [];
        foreach ($client->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $resources[] = $property->getName();
        }

        foreach ($resources as $resource) {
            $this->assertTrue(
                $facade->hasMethod($resource),
                "The facade has no {$resource}() accessor, so that resource is unreachable through it.",
            );
        }
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

    public function test_a_broadcast_campaign_goes_through_the_facade(): void
    {
        $this->fakeCamelMailer();
        $this->transporter->queue(['campaign' => ['id' => 7, 'status' => 'draft']]);

        $result = CamelMailer::campaigns()->createDraft([
            'stream' => 'newsletter',
            'from' => 'news@acme.com',
            'name' => 'September',
        ]);

        // The planning route, which writes the campaign without sending it.
        $this->assertSame('/api/v2/server/campaigns', $this->transporter->lastRequest()['path']);
        $this->assertSame('draft', $result['campaign']['status']);
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
