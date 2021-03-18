<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GetUser
{
    /**
     * @Route(methods={"GET"}, "/api/user/{userId}")
     */
    public function getUser(string $userId): JsonResponse
    {
        return new JsonResponse([
            'id' => $userId,
        ]);
    }
}
