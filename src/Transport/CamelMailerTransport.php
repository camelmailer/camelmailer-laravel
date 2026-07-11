<?php

declare(strict_types=1);

namespace CamelMailer\Laravel\Transport;

use CamelMailer\Client;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Header\HeaderInterface;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\MessageConverter;

/**
 * A Symfony Mailer transport that delivers Laravel mail through the
 * CamelMailer API (`MAIL_MAILER=camelmailer`).
 */
final class CamelMailerTransport extends AbstractTransport
{
    /**
     * Message headers that are transported structurally and must not be
     * duplicated into the API `headers` map.
     */
    private const EXCLUDED_HEADERS = [
        'from', 'to', 'cc', 'bcc', 'reply-to', 'subject', 'date',
        'message-id', 'return-path', 'sender', 'mime-version',
        'content-type', 'content-transfer-encoding',
        self::TAG_HEADER, self::STREAM_HEADER,
    ];

    public const TAG_HEADER = 'x-camelmailer-tag';

    public const STREAM_HEADER = 'x-camelmailer-stream';

    public function __construct(private readonly Client $client)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $original = $message->getOriginalMessage();

        if (! $original instanceof Message) {
            throw new TransportException('The CamelMailer transport only supports MIME messages.');
        }

        $email = MessageConverter::toEmail($original);

        $result = $this->client->emails->send($this->payload($email));

        $messageId = $result['message_id'];

        if (is_int($messageId) || is_string($messageId)) {
            $message->setMessageId((string) $messageId);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Email $email): array
    {
        $from = $email->getFrom();

        $payload = [
            'from' => $from === [] ? null : $this->address($from[0]),
            'to' => $this->addresses($email->getTo()),
        ];

        if ($email->getCc() !== []) {
            $payload['cc'] = $this->addresses($email->getCc());
        }

        if ($email->getBcc() !== []) {
            $payload['bcc'] = $this->addresses($email->getBcc());
        }

        if ($email->getReplyTo() !== []) {
            $payload['reply_to'] = $this->addresses($email->getReplyTo());
        }

        if ($email->getSubject() !== null) {
            $payload['subject'] = $email->getSubject();
        }

        $textBody = $this->stringify($email->getTextBody());

        if ($textBody !== null) {
            $payload['text_body'] = $textBody;
        }

        $htmlBody = $this->stringify($email->getHtmlBody());

        if ($htmlBody !== null) {
            $payload['html_body'] = $htmlBody;
        }

        $attachments = [];

        foreach ($email->getAttachments() as $attachment) {
            $attachments[] = [
                'name' => $attachment->getFilename() ?? 'attachment',
                'content_type' => $attachment->getMediaType().'/'.$attachment->getMediaSubtype(),
                'data_base64' => base64_encode($attachment->getBody()),
            ];
        }

        if ($attachments !== []) {
            $payload['attachments'] = $attachments;
        }

        $headers = [];

        foreach ($email->getHeaders()->all() as $header) {
            if (! $header instanceof HeaderInterface) {
                continue;
            }

            $name = strtolower($header->getName());

            if ($name === self::TAG_HEADER) {
                $payload['tag'] = $header->getBodyAsString();

                continue;
            }

            if ($name === self::STREAM_HEADER) {
                $payload['stream'] = $header->getBodyAsString();

                continue;
            }

            if (! in_array($name, self::EXCLUDED_HEADERS, true)) {
                $headers[$header->getName()] = $header->getBodyAsString();
            }
        }

        if ($headers !== []) {
            $payload['headers'] = $headers;
        }

        return $payload;
    }

    /**
     * @param  resource|string|null  $body
     */
    private function stringify(mixed $body): ?string
    {
        if ($body === null || is_string($body)) {
            return $body;
        }

        $contents = stream_get_contents($body);

        return $contents === false ? null : $contents;
    }

    /**
     * @return array<string, string>|string
     */
    private function address(Address $address): array|string
    {
        if ($address->getName() !== '') {
            return ['email' => $address->getAddress(), 'name' => $address->getName()];
        }

        return $address->getAddress();
    }

    /**
     * @param  array<Address>  $addresses
     * @return list<array<string, string>|string>
     */
    private function addresses(array $addresses): array
    {
        return array_map(fn (Address $address): array|string => $this->address($address), array_values($addresses));
    }

    public function __toString(): string
    {
        return 'camelmailer';
    }
}
