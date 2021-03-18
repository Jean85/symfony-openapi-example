<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GetUser
{
    /**
     * @Route(methods={"GET"}, "/api/users/{userId}", requirements={"userId": "^\d+$"})
     */
    public function getUser(int $userId): JsonResponse
    {
        return new JsonResponse([
            'id' => $userId,
        ]);
    }
}
