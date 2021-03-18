<?php

declare(strict_types=1);

namespace Tests\Functional;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetUserTest extends BaseFunctionalTestCase
{
    public function testGetUser(): void
    {
        $client = self::createClient();

        $client->request(Request::METHOD_GET, '/api/user/jean85');

        $this->assertStatusCode(Response::HTTP_OK, $client);
    }
}
