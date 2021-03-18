<?php

namespace Tests\Functional;

use Facile\SymfonyFunctionalTestCase\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class GetUserTest extends WebTestCase
{
    public function testGetUser(): void
    {
        $client = self::createClient();
        
        $client->request(Request::METHOD_GET, '/api/user/jean85');
        
        $this->assertStatusCodeIsSuccessful($client);
    }
}
