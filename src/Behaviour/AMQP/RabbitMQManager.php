<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\AMQP;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;

final class RabbitMQManager implements AmqpManager
{
    private AMQPStreamConnection $connection;

    public function __construct(AMQPStreamConnection $connection)
    {
        $this->connection = $connection;
    }

    public function consume(string $queue, string $exchange): array
    {
        $channel = $this->connection->channel();
        $messages = [];

        $channel->basic_consume(
            $queue,
            '',
            false,
            false,
            false,
            false,
            static function (AMQPMessage $message) use (&$messages)
            {
                $channel = $message->delivery_info['channel'];
                $deliveryTag = $message->delivery_info['delivery_tag'];

                \array_push(
                    $messages,
                    \json_decode(
                        $message->body,
                        true)
                );

                $channel->basic_ack($deliveryTag);
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
