<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\ApiRest;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use function GuzzleHttp\uri_template;

final class GuzzleHttpApiCallsManager implements ApiCallsManager
{
    private Client $client;
    /**
     * @var ResponseInterface[]
     */
    private array $responses;
    private string $host;

    public function __construct(Client $client, string $host)
    {
        $this->client = $client;
        $this->host = $host;
        $this->responses = [];
    }

    /**
     * @inheritDoc
     */
    public function responses(): array
    {
        return $this->responses;
    }

    public function response(string $key): ?ResponseInterface
    {
        if (false === \array_key_exists($key, $this->responses)) {
            return null;
        }

        return $this->responses[$key];
    }

    public function clearResponses(): void
    {
        $this->responses = [];
    }

    /**
     * @inheritDoc
     */
    public function post(string $uriPath, array $body, array $uriVariables = []): string
    {
        try {
            return $this->storeResponseUnderAccessKey(
                $this->client->post(
                    $this->buildUri($uriPath, $uriVariables),
                    [
                        RequestOptions::JSON => $body,
                    ],
                ),
            );
        } catch (BadResponseException $e) {
            return $this->storeResponseUnderAccessKey($e->getResponse());
        }
    }

    /**
     * @inheritDoc
     */
    public function put(string $uriPath, array $body, array $uriVariables = []): string
    {
        try {
            return $this->storeResponseUnderAccessKey(
                $this->client->put(
                    $this->buildUri($uriPath, $uriVariables),
                    [
                        RequestOptions::JSON => $body,
                    ],
                ),
            );
        } catch (BadResponseException $e) {
            return $this->storeResponseUnderAccessKey($e->getResponse());
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(string $uriPath, array $uriVariables = []): string
    {
        try {
            return $this->storeResponseUnderAccessKey(
                $this->client->delete(
                    $this->buildUri($uriPath, $uriVariables),
                    [],
                ),
            );
        } catch (BadResponseException $e) {
            return $this->storeResponseUnderAccessKey($e->getResponse());
        }
    }

    private function buildUri(string $uriPath, array $uriVariables): string
    {
        $uri = $this->host . $uriPath;
        $uri = uri_template($uri, $uriVariables);

        return $uri;
    }

    private function storeResponseUnderAccessKey(ResponseInterface $response): string
    {
        $key = (string) Uuid::uuid4();
        $this->responses[$key] = $response;

        return $key;
    }
}
