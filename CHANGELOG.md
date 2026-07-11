# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.0] - 2026-07-11

### Added

- `MAIL_MAILER=camelmailer` Symfony Mailer transport: Mailables and Notifications are delivered through the CamelMailer API, including attachments, CC/BCC, reply-to, custom headers and the `X-CamelMailer-Tag` / `X-CamelMailer-Stream` headers.
- `CamelMailerServiceProvider` with `config/camelmailer.php` (`CAMELMAILER_API_KEY`, `CAMELMAILER_BASE_URL`) and a `CamelMailer\Client` container singleton.
- `CamelMailer` facade exposing the SDK resources (`emails()`, `templates()`, `streams()`, `stats()`, `bounces()`, `dmarc()`, `ping()`, `server()`).

[Unreleased]: https://github.com/camelmailer/camelmailer-laravel/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/camelmailer/camelmailer-laravel/releases/tag/v0.1.0
