<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\Behat\Context\AmqpFeatureSteps;

use Assert\Assertion;

trait PublishCommand
{
    /** @Then Publish command :commandName in :commandsQueue queue */
    public function publishCommand(string $commandName, string $commandsQueue)
    {
        $commands = $this->amqpManager->consume($commandsQueue);

        foreach ($commands as $command) {
            Assertion::same($command['data']['type'], $commandName);
        }
    }
}
