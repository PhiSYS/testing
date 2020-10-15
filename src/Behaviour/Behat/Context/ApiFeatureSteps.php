<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\Behat\Context;

use Assert\Assert;
use Assert\Assertion;
use DosFarma\Testing\Behaviour\ApiRest\ApiCallsManager;
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

    /** @Then I receive a :expectedResponseCode code response */
    public function iReceiveAResponse(string $expectedResponseCode)
    {
        foreach ($this->apiCallsManager->responses() as $key => $response) {
            Assertion::notNull($response);
            Assertion::same(
                $response->getStatusCode(),
                \intval($expectedResponseCode),
            );

            if (202 === $response->getStatusCode()) {
                $this->assertMessageIdInAcceptedResponse($response);
            }
        }
    }

    private function assertMessageIdInAcceptedResponse(ResponseInterface $response): void
    {
        $bodyContent = $response->getBody()->getContents();
        Assertion::isJsonString($bodyContent);
        $responseData = \json_decode($bodyContent, true);

        Assert::lazy()
            ->that($responseData)->keyExists('message_id')
            ->that($responseData['message_id'])->uuid()
            ->verifyNow()
        ;
    }
}
