<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Controller\UserController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends BaseFunctionalTestCase
{
    public function testGetUserWithWrongParameter(): void
    {
        $client = $this->createOpenApiClient();

        $client->request(Request::METHOD_GET, '/api/users/invalid');

        $this->assertStatusCode(Response::HTTP_NOT_FOUND, $client);
    }

    /**
     * @dataProvider userIdDataProvider
     */
    public function testGetUser(int $userId): void
    {
        $client = $this->createOpenApiClient();

        $client->request(Request::METHOD_GET, '/api/users/' . $userId);

        $this->assertStatusCode(Response::HTTP_OK, $client);
        $data = $this->decodeResponse($client);
        $this->assertSame(UserController::USER_DATA[$userId], $data);
    }

    /**
     * @return array{int}[]
     */
    public function userIdDataProvider(): array
    {
        return [
            'User with minimal data' => [1],
            'User with complete data' => [2],
        ];
    }
}
