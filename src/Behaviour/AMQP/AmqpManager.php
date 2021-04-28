<?php
declare(strict_types=1);

namespace PhiSYS\Testing\Behaviour\AMQP;

interface AmqpManager
{
    public function publish(Message $message, string $exchange, string $routingKey = ''): void;

    public function consume(string $queue): array;

    public function purge(string $queue): void;
}
