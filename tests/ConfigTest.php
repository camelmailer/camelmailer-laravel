<?php

declare(strict_types=1);

namespace CamelMailer\Laravel\Tests;

final class ConfigTest extends TestCase
{
    public function test_the_default_config_is_merged(): void
    {
        $this->assertSame('cm_test_key', config('camelmailer.api_key'));
        $this->assertSame('https://app.camelmailer.com', config('camelmailer.base_url'));
    }

    public function test_the_config_file_can_be_published(): void
    {
        $target = $this->app->configPath('camelmailer.php');

        if (file_exists($target)) {
            unlink($target);
        }

        $this->artisan('vendor:publish', ['--tag' => 'camelmailer-config'])->assertSuccessful();

        $this->assertFileExists($target);

        /** @var array{api_key: string|null, base_url: string} $published */
        $published = require $target;
        $this->assertArrayHasKey('api_key', $published);
        $this->assertArrayHasKey('base_url', $published);

        unlink($target);
    }
}
