<?php

declare(strict_types=1);

namespace CamelMailer\Laravel\Facades;

use CamelMailer\ApiObject;
use CamelMailer\Client;
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

    /**
     * Broadcast campaigns.
     *
     * `createDraft()` writes a campaign and waits; `createAndSend()` expands
     * it to the stream's subscribers before the call returns.
     */
    public static function campaigns(): Campaigns
    {
        return self::client()->campaigns;
    }

    /**
     * The opt-in audience of a broadcast stream. A broadcast send to an
     * address that is not subscribed is refused.
     */
    public static function subscribers(): Subscribers
    {
        return self::client()->subscribers;
    }

    /**
     * Template layouts: the wrapper shared by every template that uses it.
     */
    public static function layouts(): Layouts
    {
        return self::client()->layouts;
    }

    /**
     * Inbound mail, and outbound mail the spam filter put on hold.
     */
    public static function inbound(): Inbound
    {
        return self::client()->inbound;
    }

    /**
     * The server's own request log and tag index.
     */
    public static function logs(): Logs
    {
        return self::client()->logs;
    }

    private static function client(): Client
    {
        /** @var Client */
        return self::getFacadeRoot();
    }
}
