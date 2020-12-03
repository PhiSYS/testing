<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\Bus;

use PcComponentes\Ddd\Util\Message\AggregateMessage;
use PcComponentes\Ddd\Util\Message\Message;
use PcComponentes\Ddd\Util\Message\SimpleMessage;

interface BusManager
{
    public function publishEvent(AggregateMessage $event): void;

    public function handleEvent(array $eventDatum): void;

    public function deserializeEvent(array $eventDatum): AggregateMessage;

    public function publishCommand(SimpleMessage $command): void;

    public function handleCommand(array $commandDatum): void;

    public function deserializeCommand(array $commandDatum): SimpleMessage;

    public function serializeMessage(Message $message);
}