<?php

declare(strict_types=1);

namespace Tests\Functional;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetUserTest extends BaseFunctionalTestCase
{
    public function testGetUserWithWrongParameter(): void
    {
        $client = $this->createOpenApiClient();

        $client->request(Request::METHOD_GET, '/api/users/invalid');

        $this->assertStatusCode(Response::HTTP_NOT_FOUND, $client);
    }

    public function testGetUser(): void
    {
        $client = $this->createOpenApiClient();

        $client->request(Request::METHOD_GET, '/api/users/1');

        $this->assertStatusCode(Response::HTTP_OK, $client);
    }
}
