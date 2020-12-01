<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\Behat\Context\AmqpFeatureSteps;

use Assert\Assertion;
use Behat\Gherkin\Node\TableNode;

trait PublishDomainEvent
{
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
}
