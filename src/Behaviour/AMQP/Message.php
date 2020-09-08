<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\AMQP;

final class Message
{
    private string $body;
    private array $properties;

    public function __construct(string $body, array $properties = [])
    {
        $this->body = $body;
        $this->properties = $properties;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function properties(): array
    {
        return $this->properties;
    }
}
