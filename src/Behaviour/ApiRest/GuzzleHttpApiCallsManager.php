<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\ApiRest;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
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
     * @return ResponseInterface[]
     */
    public function responses(): array
    {
        return $this->responses;
    }

    public function clearResponses(): void
    {
        $this->responses = [];
    }

    public function post(string $uriPath, array $body, array $uriVariables = []): void
    {
        try {
            $this->responses[] = $this->client->post(
                $this->buildUri($uriPath, $uriVariables),
                [
                    RequestOptions::JSON => $body,
                ],
            );
        } catch (BadResponseException $e) {
            $this->responses[] = $e->getResponse();
        }
    }

    public function put(string $uriPath, array $body, array $uriVariables = []): void
    {
        try {
            $this->responses[] = $this->client->put(
                $this->buildUri($uriPath, $uriVariables),
                [
                    RequestOptions::JSON => $body,
                ],
            );
        } catch (BadResponseException $e) {
            $this->responses[] = $e->getResponse();
        }
    }

    public function delete(string $uriPath, array $uriVariables = []): void
    {
        try {
            $this->responses[] = $this->client->delete(
                $this->buildUri($uriPath, $uriVariables),
                [],
            );
        } catch (BadResponseException $e) {
            $this->responses[] = $e->getResponse();
        }
    }

    private function buildUri(string $uriPath, array $uriVariables): string
    {
        $uri = $this->host . $uriPath;
        $uri = uri_template($uri, $uriVariables);

        return $uri;
    }
}
