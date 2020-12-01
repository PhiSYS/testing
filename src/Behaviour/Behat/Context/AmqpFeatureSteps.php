<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\Behat\Context;

use DosFarma\Testing\Behaviour\AMQP\AmqpManager;
use DosFarma\Testing\Behaviour\Behat\Context\AmqpFeatureSteps\PublishCommand;
use DosFarma\Testing\Behaviour\Behat\Context\AmqpFeatureSteps\PublishDomainEvent;

/**
 * @deprecated
 */
trait AmqpFeatureSteps
{
    use PublishCommand;
    use PublishDomainEvent;

    private AmqpManager $amqpManager;

    protected function amqpManager(): AmqpManager
    {
        return $this->amqpManager;
    }
}
