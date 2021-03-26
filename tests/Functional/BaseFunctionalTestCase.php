<?php

declare(strict_types=1);

namespace Tests\Functional;

use Facile\SymfonyFunctionalTestCase\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Tests\OpenApiClient;

abstract class BaseFunctionalTestCase extends WebTestCase
{
    protected function createOpenApiClient(): OpenApiClient
    {
        $openApiClient = $this->getContainer()->get(OpenApiClient::class);
        $this->assertInstanceOf(OpenApiClient::class, $openApiClient);

        return $openApiClient;
    }

    public function assertStatusCode(int $expectedStatusCode, KernelBrowser $client, string $message = null): void
    {
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response, 'Response missing from client');
        $content = $this->getResponseContent($response);

        parent::assertStatusCode($expectedStatusCode, $client, $message ?? $content);
    }

    private function getResponseContent(Response $response): string
    {
        $content = $response->getContent();

        if ($content && $content[0] === '{') {
            $decoded = \Safe\json_decode($content, true);
            $content = \Safe\substr(print_r($decoded, true), 6, -1);
        }

        return $content ?: 'Response had no content available';
    }

    /**
     * @return scalar[]|scalar[][]
     */
    protected function decodeResponse(OpenApiClient $client): array
    {
        $content = $client->getResponse()->getContent();
        $this->assertNotFalse($content);
        $this->assertJson($content);

        $decodedResponse = \Safe\json_decode($content, true);

        $this->assertIsArray($decodedResponse, 'Content of response was not a JSON object, got: ' . $content);

        return $decodedResponse;
    }
}
