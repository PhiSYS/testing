<?php
declare(strict_types=1);

namespace PhiSYS\Testing\Behaviour\Behat\Context;

use Assert\Assertion;
use Assert\InvalidArgumentException;
use Assert\LazyAssertionException;
use PhiSYS\Testing\Behaviour\ApiRest\ApiCallsManager;
use Psr\Http\Message\ResponseInterface;

trait ApiFeatureSteps
{
    private ApiCallsManager $apiCallsManager;
    private ?string $uriPath;

    /** @Given An api rest uri path :uriPath */
    public function anApiRestUri(string $uriPath)
    {
        $this->uriPath = $uriPath;
    }

    /** @Then I receive a :expectedStatusCode code response */
    public function iReceiveAResponse(string $expectedStatusCode)
    {
        $expectedStatusCode = (int) $expectedStatusCode;

        foreach ($this->apiCallsManager->responses() as $response) {
            Assertion::notNull($response);

            try {
                $statusCode = $response->getStatusCode();
                Assertion::same(
                    $statusCode,
                    $expectedStatusCode,
                    \sprintf(
                        'Status code "%d" is not the same as expected "%d"',
                        $statusCode,
                        $expectedStatusCode,
                    ),
                );

                if (202 === $statusCode) {
                    $this->assertMessageIdInAcceptedResponse($response);
                }
            } catch (LazyAssertionException $e) {
                throw new LazyAssertionException(
                    $this->addResponseDataToMessage($e->getMessage(), $response),
                    $e->getErrorExceptions(),
                );
            } catch (InvalidArgumentException $e) {
                throw new InvalidArgumentException(
                    $this->addResponseDataToMessage($e->getMessage(), $response),
                    $e->getCode(),
                    $e->getPropertyPath(),
                    $e->getValue(),
                );
            }
        }
    }

    private function assertMessageIdInAcceptedResponse(ResponseInterface $response): void
    {
        $response->getBody()->rewind();
        $bodyContent = $response->getBody()->getContents();
        Assertion::isJsonString($bodyContent);
        $responseData = \json_decode($bodyContent, true);

        $messageIdKey = 'message_id';
        Assertion::keyExists(
            $responseData,
            $messageIdKey,
            \sprintf(
                'Body does not contain required accepted response field "%s"',
                $messageIdKey,
            ),
        );
        Assertion::uuid(
            $responseData[$messageIdKey],
            \sprintf(
                'Accepted response %s "%s" is not a valid Uuid',
                $messageIdKey,
                (string) $responseData[$messageIdKey],
            ),
        );
    }

    private function addResponseDataToMessage(string $message, ResponseInterface $response): string
    {
        return \sprintf(
            '%s%sResponse data:%s%s',
            $message,
            \PHP_EOL,
            \PHP_EOL,
            $this->serializeResponse($response),
        );
    }

    private function serializeResponse(ResponseInterface $response): string
    {
        $response->getBody()->rewind();
        $body = $response->getBody()->getContents();
        $decodedJsonBody = \json_decode($body, true);

        if ('NULL' !== $decodedJsonBody) {
            $body = $decodedJsonBody;
        }

        return \json_encode(
            [
                'protocol_version' => $response->getProtocolVersion(),
                'status' => $response->getStatusCode(),
                'reason' => $response->getReasonPhrase(),
                'headers' => $response->getHeaders(),
                'body' => $body,
            ],
            \JSON_PRETTY_PRINT,
        );
    }
}
