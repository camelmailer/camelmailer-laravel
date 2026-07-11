<?php

declare(strict_types=1);

namespace CamelMailer\Laravel;

use CamelMailer\CamelMailer;
use CamelMailer\Client;
use CamelMailer\Laravel\Transport\CamelMailerTransport;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

final class CamelMailerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/camelmailer.php', 'camelmailer');

        $this->app->singleton(Client::class, static function (Application $app): Client {
            $repository = $app->make('config');
            $settings = $repository instanceof Repository ? (array) $repository->get('camelmailer', []) : [];

            $apiKey = $settings['api_key'] ?? null;
            $baseUrl = $settings['base_url'] ?? null;

            return CamelMailer::client(
                is_string($apiKey) ? $apiKey : '',
                baseUrl: is_string($baseUrl) ? $baseUrl : CamelMailer::DEFAULT_BASE_URL,
            );
        });

        $this->app->alias(Client::class, 'camelmailer');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/camelmailer.php' => $this->app->configPath('camelmailer.php'),
            ], 'camelmailer-config');
        }

        Mail::extend('camelmailer', function (): CamelMailerTransport {
            $client = $this->app->make(Client::class);

            if (! $client instanceof Client) {
                throw new RuntimeException('The CamelMailer client is not bound to the container.');
            }

            return new CamelMailerTransport($client);
        });
    }
}
