<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserController
{
    public const USER_DATA = [
        1 => [
            'id' => 1,
            'firstName' => 'Alessandro',
            'lastName' => 'Lai',
            'email' => 'test@alessandrolai.dev',
            'emailVerified' => true,
            'createDate' => '2020-01-01',
        ],
        2 => [
            'id' => 2,
            'firstName' => 'Mario',
            'lastName' => 'Rossi',
            'email' => 'test2@alessandrolai.dev',
            'emailVerified' => false,
            'dateOfBirth' => '1990-01-01',
            'createDate' => '2020-01-01',
        ],
    ];

    /**
     * @Route(methods={"GET"}, "/api/users/{userId}", requirements={"userId": "^\d+$"})
     */
    public function get(int $userId): JsonResponse
    {
        if (! array_key_exists($userId, self::USER_DATA)) {
            throw new NotFoundHttpException('User not found');
        }

        return new JsonResponse(self::USER_DATA[$userId]);
    }

    /**
     * @Route(methods={"POST"}, "/api/user")
     */
    public function post(): JsonResponse
    {
        // it doesn't really work, I know!

        return new JsonResponse();
    }
}
