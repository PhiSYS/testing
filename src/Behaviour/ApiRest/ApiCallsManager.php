<?php
declare(strict_types=1);

namespace PhiSYS\Testing\Behaviour\ApiRest;

use Psr\Http\Message\ResponseInterface;

interface ApiCallsManager
{
    /**
     * @return ResponseInterface[]
     */
    public function responses(): array;

    public function response(int $key): ?ResponseInterface;

    /**
     * @return int Response access key
     */
    public function get(string $uriPath, array $uriVariables = [], array $queryVariables = [], array $headers = []): int;

    /**
     * @return int Response access key
     */
    public function post(string $uriPath, array $body, array $uriVariables = [], array $headers = []): int;

    /**
     * @return int Response access key
     */
    public function put(string $uriPath, array $body, array $uriVariables = [], array $headers = []): int;

    /**
     * @return int Response access key
     */
    public function delete(string $uriPath, array $uriVariables = [], array $headers = []): int;
}
