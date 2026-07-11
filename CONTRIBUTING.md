# Contributing

## Dev setup

```bash
composer install
```

Until `camelmailer/camelmailer` is published on Packagist, it is pulled from GitHub via the `repositories` entry in `composer.json`.

## Commands

```bash
composer test      # Orchestra Testbench test suite (no network)
composer lint      # Laravel Pint (check)
composer lint:fix  # Laravel Pint (fix)
composer analyse   # PHPStan, level max
```

## Conventions

- Tests first: transport behaviour is tested against a fake SDK transporter — no HTTP in tests.
- PHP 8.1+ and Laravel 10/11/12 compatibility (see the CI matrix).
- Keep the transport mapping in lockstep with the CamelMailer OpenAPI spec.
