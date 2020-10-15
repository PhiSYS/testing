<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\Behat\Context;

use Assert\Assertion;
use Behat\Gherkin\Node\TableNode;
use DosFarma\Testing\Behaviour\AMQP\AmqpManager;

trait AmqpFeatureSteps
{
    private AmqpManager $amqpManager;

    /** @Then Publish domain :eventType event in :eventsQueue queue: */
    public function publishDomainEvent(TableNode $table, string $eventType, string $eventsQueue)
    {
        $events  = $this->amqpManager->consume($eventsQueue);

        $aggregateIds = $table->getColumn(0);
        foreach ($events as $event) {
            Assertion::inArray($event['data']['attributes']['aggregate_id'], $aggregateIds);
            Assertion::same($event['data']['type'], $eventType);
        }
    }

    /** @Then Publish command :commandName in :commandsQueue queue */
    public function publishCommand(string $commandName, string $commandsQueue)
    {
        $commands = $this->amqpManager->consume($commandsQueue);

        foreach ($commands as $command) {
            Assertion::same($command['data']['type'], $commandName);
        }
    }
}
