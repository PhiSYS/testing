<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\AMQP;

interface AmqpManager
{
    public function consume(string $queue, string $exchange);
}
