<?php

declare(strict_types=1);

namespace CamelMailer\Laravel\Facades;

use CamelMailer\ApiObject;
use CamelMailer\Client;
use CamelMailer\Resources\Bounces;
use CamelMailer\Resources\Dmarc;
use CamelMailer\Resources\Emails;
use CamelMailer\Resources\Stats;
use CamelMailer\Resources\Streams;
use CamelMailer\Resources\Templates;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ApiObject ping()
 * @method static ApiObject server()
 *
 * @see Client
 */
final class CamelMailer extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Client::class;
    }

    public static function emails(): Emails
    {
        return self::client()->emails;
    }

    public static function templates(): Templates
    {
        return self::client()->templates;
    }

    public static function streams(): Streams
    {
        return self::client()->streams;
    }

    public static function stats(): Stats
    {
        return self::client()->stats;
    }

    public static function bounces(): Bounces
    {
        return self::client()->bounces;
    }

    public static function dmarc(): Dmarc
    {
        return self::client()->dmarc;
    }

    private static function client(): Client
    {
        /** @var Client */
        return self::getFacadeRoot();
    }
}
