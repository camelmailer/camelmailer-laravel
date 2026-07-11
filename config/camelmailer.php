<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | CamelMailer API Key
    |--------------------------------------------------------------------------
    |
    | A server API key of your CamelMailer mail server. Create one in the
    | dashboard under Server → Credentials (type: API).
    |
    */

    'api_key' => env('CAMELMAILER_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | Defaults to the CamelMailer cloud. Point this at your own instance
    | when self-hosting, e.g. https://mail.example.com
    |
    */

    'base_url' => env('CAMELMAILER_BASE_URL', 'https://app.camelmailer.com'),

];
