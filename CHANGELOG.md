# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Laravel 13 support: `illuminate/mail` and `illuminate/support` ^13.0, and
  `symfony/mailer` ^8.0, which Laravel 13 ships with. The transport needed no
  change; CI now runs the suite against Laravel 13 on PHP 8.3 and 8.4.

## [0.2.0] - 2026-09-14

### Added

- The facade exposes the five resources the PHP SDK gained in 0.2.0:
  `campaigns()`, `subscribers()`, `layouts()`, `inbound()` and `logs()`.
  A test now reflects over the client and asserts the facade has an
  accessor for every resource, so the package cannot silently fall behind
  again.

### Changed

- Requires `camelmailer/camelmailer` 0.2.3, whose `TransporterInterface`
  takes request headers. A custom transporter implementing that interface
  needs the extra `array $headers = []` parameter.

## [0.1.0] - 2026-07-11

### Added

- `MAIL_MAILER=camelmailer` Symfony Mailer transport: Mailables and Notifications are delivered through the Camelmailer API, including attachments, CC/BCC, reply-to, custom headers and the `X-CamelMailer-Tag` / `X-CamelMailer-Stream` headers.
- `CamelMailerServiceProvider` with `config/camelmailer.php` (`CAMELMAILER_API_KEY`, `CAMELMAILER_BASE_URL`) and a `CamelMailer\Client` container singleton.
- `CamelMailer` facade exposing the SDK resources (`emails()`, `templates()`, `streams()`, `stats()`, `bounces()`, `dmarc()`, `ping()`, `server()`).

[Unreleased]: https://github.com/camelmailer/camelmailer-laravel/compare/v0.2.0...HEAD
[0.2.0]: https://github.com/camelmailer/camelmailer-laravel/releases/tag/v0.2.0
[0.1.0]: https://github.com/camelmailer/camelmailer-laravel/releases/tag/v0.1.0
