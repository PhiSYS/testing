<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\Behat\Context\AmqpFeatureSteps;

use Assert\Assertion;
use DosFarma\Testing\Behaviour\AMQP\AmqpManager;

trait PublishCommand
{
    /** @Then Publish command :commandName in :commandsQueue queue */
    public function publishCommand(string $commandName, string $commandsQueue)
    {
        $commands = $this->amqpManager()->consume($commandsQueue);

        foreach ($commands as $command) {
            Assertion::same($command['data']['type'], $commandName);
        }
    }

    abstract protected function amqpManager(): AmqpManager;
}
