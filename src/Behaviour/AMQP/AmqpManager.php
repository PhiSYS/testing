<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\AMQP;

interface AMQPManager
{
    public function consume(string $queue): array;
}
