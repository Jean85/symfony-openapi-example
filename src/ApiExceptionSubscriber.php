<?php

declare(strict_types=1);

namespace App;

use Crell\ApiProblem\ApiProblem;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\EventListener\ErrorListener;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @return array<string, array{string, int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', self::getSymfonyExceptionSubscriberPriority() + 1],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $apiProblem = $this->createApiProblem($event->getThrowable());
        $response = new JsonResponse(
            $apiProblem, 
            $apiProblem->getStatus(),
            ['Content-type' => ApiProblem::CONTENT_TYPE_JSON]
        );

        $event->setResponse($response);
    }

    /**
     * @see RFC 7808
     */
    private function createApiProblem(\Throwable $error): ApiProblem
    {
        $apiProblem = new ApiProblem('Unknown error: ' . $error->getMessage(), 'https://alessandrolai.dev/problem/unknown');

        if ($error instanceof HttpExceptionInterface) {
            $apiProblem->setStatus($error->getStatusCode());
            $apiProblem->setTitle($error->getMessage());
            $apiProblem->setType('https://alessandrolai.dev/problem/http-common-' . $error->getStatusCode());

            return $apiProblem;
        }

        $apiProblem->setStatus(Response::HTTP_INTERNAL_SERVER_ERROR);

        if ($error instanceof ValidationFailed) {
            $apiProblem->setStatus(Response::HTTP_BAD_REQUEST);
            $apiProblem->setTitle('OpenAPI validation failed: ' . $error->getMessage());
            $apiProblem->setType('https://alessandrolai.dev/problem/openapi-validation-failed');
        }

        return $apiProblem;
    }

    private static function getSymfonyExceptionSubscriberPriority(): int
    {
        /** @var array{string, int}[] */
        $symfonyListenerSubscribedEvents = ErrorListener::getSubscribedEvents()[KernelEvents::EXCEPTION];

        foreach ($symfonyListenerSubscribedEvents as $subscription) {
            if ($subscription[0] === 'onKernelException') {
                return $subscription[1];
            }
        }

        throw new \RuntimeException('Cannot find the Symfony listener priority');
    }
}
