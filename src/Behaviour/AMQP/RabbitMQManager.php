<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\AMQP;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;

final class RabbitMQManager implements AmqpManager
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function __construct(string $host, int $port, string $user, string $pass)
    {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $pass);
        $this->channel = $this->connection->channel();
    }

    public function consume(string $queue, string $exchange)
    {
        $callback = function($msg){
            echo " [x] Received ", $msg->body, "\n";
            $job = \json_decode($msg->body, $assocForm=true);
            sleep($job['sleep_period']);
            echo " [x] Done", "\n";
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        //$this->channel->basic_qos(null, 1, null);
        $this->channel->queue_declare($queue, false, false, true, true);
        $this->channel->exchange_declare($exchange, AMQPExchangeType::TOPIC, false, true, false);

        $this->channel->queue_bind($queue, $exchange);

        $this->channel->basic_consume($queue, false, true, false, $callback);

        $this->channel->close();
        $this->connection->close();
    }
}

