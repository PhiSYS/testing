<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\SymfonyMessenger;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

final class SymfonyMessengerConsumeManager
{
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function __invoke(string $transport): void
    {
        $this->app->run(
            $this->messageConsumeCommand($transport),
        );
    }

    private function messageConsumeCommand(string $transport): ArrayInput
    {
        return new ArrayInput(
            [
                'command' => \sprintf(
                    'messenger:consumer %s --limit=1',
                    $transport,
                ),
            ]
        );
    }
}
