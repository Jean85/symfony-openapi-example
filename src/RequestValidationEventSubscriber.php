<?php

declare(strict_types=1);

namespace App;

use League\OpenAPIValidation\PSR7\RequestValidator;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
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
            KernelEvents::CONTROLLER => 'validateRequest',
        ];
    }

    public function validateRequest(ControllerEvent $event): void
    {
        if (! $event->isMasterRequest()) {
            return;
        }

        $psr7request = $this->psrHttpFactory->createRequest($event->getRequest());
        $this->requestValidator->validate($psr7request);
    }
}
