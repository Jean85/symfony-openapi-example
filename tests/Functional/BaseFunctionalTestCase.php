<?php

declare(strict_types=1);

namespace Tests\Functional;

use Facile\SymfonyFunctionalTestCase\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseFunctionalTestCase extends WebTestCase
{
    public function assertStatusCode(int $expectedStatusCode, KernelBrowser $client, string $message = null): void
    {
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response, 'Response missing from client');
        $content = $response->getContent() ?: 'Response had no content available';

        parent::assertStatusCode($expectedStatusCode, $client, $message ?? $content);
    }
}
