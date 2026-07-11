<?php

declare(strict_types=1);

namespace CamelMailer\Laravel\Tests;

use CamelMailer\Client;
use CamelMailer\Laravel\CamelMailerServiceProvider;
use CamelMailer\Laravel\Tests\Fixtures\FakeTransporter;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected FakeTransporter $transporter;

    protected function getPackageProviders($app): array
    {
        return [CamelMailerServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'CamelMailer' => \CamelMailer\Laravel\Facades\CamelMailer::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('camelmailer.api_key', 'cm_test_key');
        $app['config']->set('mail.default', 'camelmailer');
        $app['config']->set('mail.mailers.camelmailer', ['transport' => 'camelmailer']);
        $app['config']->set('mail.from', ['address' => 'sender@acme.com', 'name' => 'Acme']);
    }

    /**
     * Replace the SDK client with one backed by a fake transporter, so no
     * HTTP requests ever leave the test process.
     */
    protected function fakeCamelMailer(): FakeTransporter
    {
        $this->transporter = new FakeTransporter;

        $this->app->instance(Client::class, new Client($this->transporter));

        return $this->transporter;
    }
}
