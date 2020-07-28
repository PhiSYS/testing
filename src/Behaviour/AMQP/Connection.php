<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\AMQP;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class Connection
{
    public static function fromDsn(string $dsn): AMQPStreamConnection
    {
        $parsedUrl = \parse_url($dsn);

        self::assertValidUrl($parsedUrl, $dsn);
        self::assertValidAmqpDsn($parsedUrl, $dsn);

        return new AMQPStreamConnection(
            $parsedUrl['host'] ?? 'localhost',
            $parsedUrl['port'] ?? 5672,
            $parsedUrl['user'] ?? 'guest',
            $parsedUrl['pass'] ?? 'guest',
        );
    }

    private static function assertValidUrl($parsedUrl, string $dsn): void
    {
        if (false === $parsedUrl) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'The given DSN "%s" is invalid url.',
                    $dsn
                )
            );
        }
    }

    private static function assertValidAmqpDsn($parsedUrl, string $dsn): void
    {
        if ($parsedUrl['scheme'] !== 'amqp') {
            throw new \InvalidArgumentException(
                \sprintf(
                    'The given AMQP DSN "%s" is invalid.',
                    $dsn
                )
            );
        }
    }
}
