<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\ApiRest;

use Psr\Http\Message\ResponseInterface;

interface ApiCallsManager
{
    /**
     * @return ResponseInterface[]
     */
    public function responses(): array;

    public function clearResponses(): void;

    public function post(string $uriPath, array $body, array $uriVariables = []): void;

    public function put(string $uriPath, array $body, array $uriVariables = []): void;

    public function delete(string $uriPath, array $uriVariables = []): void;
}
