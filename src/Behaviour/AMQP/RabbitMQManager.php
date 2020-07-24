<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\AMQP;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;

final class RabbitMQManager implements AmqpManager
{
    private AMQPStreamConnection $connection;

    public function __construct(
        string $host,
        int $port,
        string $user,
        string $pass
    )
    {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $pass);
    }

    public function consume(string $queue, string $exchange): array
    {
        $channel = $this->connection->channel();
        $messages = [];

        $channel->basic_consume(
            $queue,
            '',
            true,
            false,
            false,
            false,
            static function (AMQPMessage $message) use (&$messages)
            {
                \array_push(
                    $messages,
                    \json_decode(
                        $message->body,
                        true)
                );
            }
        );

        while ($channel->is_consuming()) {
            try {
                $channel->wait(null, false, 2);
            } catch (AMQPTimeoutException $exception)
            {
                break;
            }
        }

        $channel->close();
        $this->connection->close();

        return $messages;
    }
}

