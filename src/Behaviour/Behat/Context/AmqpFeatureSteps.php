<?php
declare(strict_types=1);

namespace PhiSYS\Testing\Behaviour\Behat\Context;

use PhiSYS\Testing\Behaviour\AMQP\AmqpManager;
use PhiSYS\Testing\Behaviour\Behat\Context\AmqpFeatureSteps\PublishCommand;
use PhiSYS\Testing\Behaviour\Behat\Context\AmqpFeatureSteps\PublishDomainEvent;

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
