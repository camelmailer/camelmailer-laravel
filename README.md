# Camelmailer for Laravel

[![CI](https://github.com/camelmailer/camelmailer-laravel/actions/workflows/ci.yml/badge.svg)](https://github.com/camelmailer/camelmailer-laravel/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

Send Laravel mail through [Camelmailer](https://camelmailer.com) — a native `MAIL_MAILER=camelmailer` transport for Mailables and Notifications, plus a facade for the full [PHP SDK](https://github.com/camelmailer/camelmailer-php).

## Install

```bash
composer require camelmailer/camelmailer-laravel
```

Requires PHP 8.1+ and Laravel 10, 11, 12 or 13.

## Configure

`.env`:

```dotenv
MAIL_MAILER=camelmailer
CAMELMAILER_API_KEY=cm_xxxxxxxx

# Self-hosted? Point at your instance (defaults to the CamelMailer cloud):
# CAMELMAILER_BASE_URL=https://mail.example.com
```

Register the mailer in `config/mail.php`:

```php
'mailers' => [
    // ...
    'camelmailer' => [
        'transport' => 'camelmailer',
    ],
],
```

Optionally publish the package config:

```bash
php artisan vendor:publish --tag=camelmailer-config
```

## Send a Mailable

Nothing changes — your existing Mailables just go through Camelmailer:

```php
use App\Mail\OrderShipped;
use Illuminate\Support\Facades\Mail;

Mail::to('ada@example.com')->send(new OrderShipped($order));
```

```php
class OrderShipped extends Mailable
{
    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your order has shipped');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.orders.shipped');
    }
}
```

Attachments, CC/BCC, reply-to and custom headers are all mapped to the API. Two special headers let you use Camelmailer features per message:

```php
$message->getHeaders()->addTextHeader('X-CamelMailer-Tag', 'order-shipped');
$message->getHeaders()->addTextHeader('X-CamelMailer-Stream', 'transactional');
```

## Send a Notification

```php
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class InvoicePaid extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invoice paid')
            ->line('Your invoice has been paid. Thank you!');
    }
}

$user->notify(new InvoicePaid);
```

## Use the SDK directly

The facade exposes every messaging resource of the PHP SDK:

```php
use CamelMailer\Laravel\Facades\CamelMailer;

CamelMailer::emails()->send([
    'from' => 'billing@acme.com',
    'to' => ['ada@example.com'],
    'subject' => 'Your receipt',
    'html_body' => '<p>Thanks!</p>',
]);

CamelMailer::templates()->render('welcome', ['name' => 'Ada']);
CamelMailer::streams()->list();
CamelMailer::stats()->get();
CamelMailer::bounces()->list();
CamelMailer::dmarc()->summary();
CamelMailer::ping();
```

### Broadcast

```php
// A campaign is content plus an audience. The two ways to create one
// behave differently: createDraft() writes it and waits, createAndSend()
// expands it to the stream's subscribers before the call returns.
CamelMailer::campaigns()->createDraft([
    'stream' => 'newsletter',
    'from' => 'news@acme.com',
    'name' => 'September',
    'subject' => 'What shipped',
    'text_body' => 'Hello.',
]);
CamelMailer::campaigns()->createAndSend('newsletter', [
    'name' => 'Status update',
    'from' => 'news@acme.com',
    'text_body' => 'All clear.',
]);

// A broadcast send to an address that is not subscribed is refused, so
// this list is the audience.
CamelMailer::subscribers()->add('newsletter', ['address' => 'ada@example.com']);
CamelMailer::subscribers()->import('newsletter', ['ada@example.com', 'grace@example.com']);

CamelMailer::layouts()->list();
CamelMailer::inbound()->list(['status' => 'held']);
CamelMailer::logs()->tags();
```

Or inject `CamelMailer\Client` anywhere via the container.

## Error handling

```php
use CamelMailer\Exceptions\ErrorException;

try {
    Mail::to('ada@example.com')->send(new OrderShipped($order));
} catch (ErrorException $e) {
    $e->code; // 'ValidationError', 'Unauthorized', 'NotFound', ...
}
```

## Docs

Full API documentation: [camelmailer.com/docs](https://camelmailer.com/docs)

## License

MIT
