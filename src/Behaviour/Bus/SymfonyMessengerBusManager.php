<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\Bus;

use Assert\Assert;
use Assert\Assertion;
use PcComponentes\Ddd\Util\Message\AggregateMessage;
use PcComponentes\Ddd\Util\Message\Message;
use PcComponentes\Ddd\Util\Message\Serialization\JsonApi\AggregateMessageJsonApiSerializable;
use PcComponentes\Ddd\Util\Message\Serialization\JsonApi\AggregateMessageStream;
use PcComponentes\Ddd\Util\Message\Serialization\JsonApi\AggregateMessageStreamDeserializer;
use PcComponentes\Ddd\Util\Message\Serialization\JsonApi\SimpleMessageJsonApiSerializable;
use PcComponentes\Ddd\Util\Message\Serialization\JsonApi\SimpleMessageStream;
use PcComponentes\Ddd\Util\Message\Serialization\JsonApi\SimpleMessageStreamDeserializer;
use PcComponentes\Ddd\Util\Message\SimpleMessage;
use Symfony\Component\Messenger\MessageBusInterface;

final class SymfonyMessengerBusManager implements BusManager
{
    private MessageBusInterface $publishEventBus;
    private MessageBusInterface $handleEventBus;
    private MessageBusInterface $publishCommandBus;
    private MessageBusInterface $handleCommandBus;
    private AggregateMessageStreamDeserializer $aggregateMessageStreamDeserializer;
    private SimpleMessageStreamDeserializer $simpleMessageStreamDeserializer;
    private AggregateMessageJsonApiSerializable $aggregateMessageJsonApiSerializable;
    private SimpleMessageJsonApiSerializable $simpleMessageJsonApiSerializable;

    public function __construct(
        MessageBusInterface $publishEventBus,
        MessageBusInterface $handleEventBus,
        MessageBusInterface $publishCommandBus,
        MessageBusInterface $handleCommandBus,
        AggregateMessageStreamDeserializer $aggregateMessageStreamDeserializer,
        SimpleMessageStreamDeserializer $simpleMessageStreamDeserializer,
        AggregateMessageJsonApiSerializable $aggregateMessageJsonApiSerializable,
        SimpleMessageJsonApiSerializable $simpleMessageJsonApiSerializable
    ) {
        $this->publishEventBus = $publishEventBus;
        $this->handleEventBus = $handleEventBus;
        $this->publishCommandBus = $publishCommandBus;
        $this->handleCommandBus = $handleCommandBus;
        $this->aggregateMessageStreamDeserializer = $aggregateMessageStreamDeserializer;
        $this->simpleMessageStreamDeserializer = $simpleMessageStreamDeserializer;
        $this->aggregateMessageJsonApiSerializable = $aggregateMessageJsonApiSerializable;
        $this->simpleMessageJsonApiSerializable = $simpleMessageJsonApiSerializable;
    }

    public function publishEvent(AggregateMessage $event): void
    {
        $this->publishEventBus->dispatch($event);
    }

    public function handleEvent(array $eventDatum): void
    {
        $this->handleEventBus->dispatch(
            $this->aggregateMessageStreamDeserializer->unserialize(
                $this->buildAggregateMessageSteam($eventDatum),
            ),
        );
    }

    public function deserializeEvent(array $eventDatum): AggregateMessage
    {
        return $this->aggregateMessageStreamDeserializer->unserialize(
            $this->buildAggregateMessageSteam($eventDatum),
        );
    }

    public function publishCommand(SimpleMessage $command): void
    {
        $this->publishCommandBus->dispatch($command);
    }

    public function handleCommand(array $commandDatum): void
    {
        $this->handleCommandBus->dispatch(
            $this->simpleMessageStreamDeserializer->unserialize(
                $this->buildSimpleMessageSteam($commandDatum),
            ),
        );
    }

    public function deserializeCommand(array $commandDatum): SimpleMessage
    {
        return $this->simpleMessageStreamDeserializer->unserialize(
            $this->buildSimpleMessageSteam($commandDatum),
        );
    }

    private function buildAggregateMessageSteam(array $messageDatum): AggregateMessageStream
    {
        $this->assertAggregateMessage($messageDatum);

        $version = (int) (\explode('.', $messageDatum['data']['type']))[2];

        return new AggregateMessageStream(
            $messageDatum['data']['message_id'],
            $messageDatum['data']['attributes']['aggregate_id'],
            $messageDatum['data']['occurred_on'],
            $messageDatum['data']['type'],
            $version,
            \json_encode($messageDatum['data']['attributes']),
        );
    }

    private function buildSimpleMessageSteam(array $messageDatum): SimpleMessageStream
    {
        $this->assertSimpleMessage($messageDatum);

        return new SimpleMessageStream(
            $messageDatum['data']['message_id'],
            $messageDatum['data']['type'],
            \json_encode($messageDatum['data']['attributes']),
        );
    }

    private function assertAggregateMessage(array $messageDatum): void
    {
        Assert::that($messageDatum, 'messageDatum')
            ->keyExists('data', null, 'data')
        ;

        Assert::lazy()
            ->that($messageDatum['data'], 'data')
            ->keyExists('message_id', null, 'message_id')
            ->keyExists('attributes', null, 'attributes')
            ->keyExists('occurred_on', null, 'occurred_on')
            ->keyExists('type', null, 'type')
            ->verifyNow()
        ;

        Assert::that($messageDatum['data']['attributes'], 'attributes')
            ->keyExists('aggregate_id', null, 'aggregate_id')
        ;
    }

    private function assertSimpleMessage(array $messageDatum): void
    {
        Assert::that($messageDatum, 'messageDatum')
            ->keyExists('data', null, 'data')
        ;

        Assert::lazy()
            ->that($messageDatum['data'], 'data')
            ->keyExists('message_id', null, 'message_id')
            ->keyExists('attributes', null, 'attributes')
            ->keyExists('type', null, 'type')
            ->verifyNow()
        ;
    }

    public function serializeMessage(Message $message)
    {
        if ($message instanceof AggregateMessage) {
            return $this->aggregateMessageJsonApiSerializable->serialize($message);
        }

        return $this->simpleMessageJsonApiSerializable->serialize($message);
    }
}
