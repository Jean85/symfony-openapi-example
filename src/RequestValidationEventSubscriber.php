<?php

declare(strict_types=1);

namespace App;

use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use League\OpenAPIValidation\PSR7\RequestValidator;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestValidationEventSubscriber implements EventSubscriberInterface
{
    private PsrHttpFactory $psrHttpFactory;
    private RequestValidator $requestValidator;

    public function __construct(PsrHttpFactory $psrHttpFactory, RequestValidator $requestValidator)
    {
        $this->psrHttpFactory = $psrHttpFactory;
        $this->requestValidator = $requestValidator;
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'validateRequest',
        ];
    }

    public function validateRequest(RequestEvent $event): void
    {
        if (! $event->isMasterRequest()) {
            return;
        }

        $psr7request = $this->psrHttpFactory->createRequest($event->getRequest());

        try {
            $this->requestValidator->validate($psr7request);
        } catch (ValidationFailed $exception) {
            throw new BadRequestHttpException($exception->getMessage(), $exception);
        }
    }
}
