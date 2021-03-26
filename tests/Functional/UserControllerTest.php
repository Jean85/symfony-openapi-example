<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Controller\UserController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends BaseFunctionalTestCase
{
    private const VALID_POST_DATA = [
        'fistName' => 'Alessandro',
        'lastName' => 'Lai',
        'email' => 'alessandro.lai85@gmail.com',
        'birthDate' => '1990-01-01',
    ];

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

    public function testPostUser(): void
    {
        $client = $this->createOpenApiClient();
        $payload = \Safe\json_encode(self::VALID_POST_DATA);

        $client->request(Request::METHOD_POST, '/api/user', [], [], ['CONTENT_TYPE' => 'application/json'], $payload);

        $this->assertStatusCode(Response::HTTP_CREATED, $client);
        $this->assertEmpty($client->getResponse()->getContent());
    }

    /**
     * @dataProvider invalidPostDataProvider
     */
    public function testPostUserWithInvalidData(array $invalidData): void
    {
        $client = $this->createOpenApiClient();
        $payload = \Safe\json_encode($invalidData);

        $client->request(Request::METHOD_POST, '/api/user', [], [], ['CONTENT_TYPE' => 'application/json'], $payload);

        $this->assertStatusCode(Response::HTTP_BAD_REQUEST, $client);
        $response = $this->decodeResponse($client);
        $this->assertArrayHasKey('title', $response);
        $this->assertArrayHasKey('type', $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $response['status']);
    }

    /**
     * @return array<string, array{scalar[]}>
     */
    public function invalidPostDataProvider(): array
    {
        return [
            'empty' => [
                [],
            ],
            'only firstName' => [
                ['firstName' => 'Foo'],
            ],
            'invalid email' => [
                ['email' => 'baz'] + self::VALID_POST_DATA,
            ],
            'invalid birthDate' => [
                ['birthDate' => 'baz'] + self::VALID_POST_DATA,
            ],
        ];
    }
}
