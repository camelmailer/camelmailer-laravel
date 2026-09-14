<?php

declare(strict_types=1);

namespace CamelMailer\Laravel\Tests\Fixtures;

use CamelMailer\Contracts\TransporterInterface;
use CamelMailer\Exceptions\CamelMailerException;

final class FakeTransporter implements TransporterInterface
{
    /** @var list<array{method: string, path: string, body: array<mixed>|null, query: array<string, int|string>, headers: array<string, string>}> */
    public array $requests = [];

    /** @var list<array<string, mixed>|CamelMailerException> */
    private array $queue = [];

    /**
     * @param  array<string, mixed>  $data
     */
    public function queue(array $data): void
    {
        $this->queue[] = $data;
    }

    public function queueException(CamelMailerException $exception): void
    {
        $this->queue[] = $exception;
    }

    public function request(string $method, string $path, ?array $body = null, array $query = [], array $headers = []): array
    {
        $this->requests[] = compact('method', 'path', 'body', 'query', 'headers');

        $next = array_shift($this->queue);

        if ($next instanceof CamelMailerException) {
            throw $next;
        }

        return $next ?? ['message_id' => 1, 'recipients' => []];
    }

    /**
     * @return array{method: string, path: string, body: array<mixed>|null, query: array<string, int|string>, headers: array<string, string>}
     */
    public function lastRequest(): array
    {
        $last = end($this->requests);

        if ($last === false) {
            throw new \RuntimeException('No requests were recorded.');
        }

        return $last;
    }
}
