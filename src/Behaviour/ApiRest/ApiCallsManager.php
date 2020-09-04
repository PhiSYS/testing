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

    public function response(string $key): ?ResponseInterface;

    public function clearResponses(): void;

    /**
     * @return string Response access key
     */
    public function post(string $uriPath, array $body, array $uriVariables = []): string;

    /**
     * @return string Response access key
     */
    public function put(string $uriPath, array $body, array $uriVariables = []): string;

    /**
     * @return string Response access key
     */
    public function delete(string $uriPath, array $uriVariables = []): string;
}
