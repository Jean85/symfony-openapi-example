<?php

declare(strict_types=1);

namespace Tests;

use League\OpenAPIValidation\PSR7\Exception\NoResponseCode;
use League\OpenAPIValidation\PSR7\OperationAddress;
use League\OpenAPIValidation\PSR7\PathFinder;
use League\OpenAPIValidation\PSR7\ResponseValidator;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\BrowserKit\History;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class OpenApiClient extends KernelBrowser
{
    private ResponseValidator $responseValidator;
    private PsrHttpFactory $psrHttpFactory;

    public function __construct(
        ResponseValidator $responseValidator,
        PsrHttpFactory $psrHttpFactory,
        KernelInterface $kernel,
        History $history = null,
        CookieJar $cookieJar = null
    ) {
        parent::__construct($kernel, [], $history, $cookieJar);
        $this->responseValidator = $responseValidator;
        $this->psrHttpFactory = $psrHttpFactory;
    }

    protected function doRequest($request): Response
    {
        $response = parent::doRequest($request);

        $psr7request = $this->psrHttpFactory->createRequest($request);
        $psr7response = $this->psrHttpFactory->createResponse($response);
        $operationAddress = $this->findMatchingOperation($psr7request);

        $this->validateResponse($operationAddress, $psr7response, $response);

        return $response;
    }

    private function findMatchingOperation(RequestInterface $request): OperationAddress
    {
        $pathFinder = new PathFinder(
            $this->responseValidator->getSchema(),
            $request->getUri()->__toString(),
            $request->getMethod()
        );

        $matchingOperations = $pathFinder->search();

        if (count($matchingOperations) !== 1) {
            throw new \RuntimeException(
                "Number of matching API operations for URI '{$request->getUri()}' not valid: matched " .
                count($matchingOperations)
            );
        }

        return $matchingOperations[0];
    }

    private function validateResponse(OperationAddress $operationAddress, ResponseInterface $psr7response, Response $response): void
    {
        try {
            $this->responseValidator->validate($operationAddress, $psr7response);
        } catch (NoResponseCode $exception) {
            throw new \InvalidArgumentException(
                'Unexpected HTTP status code: ' . $response->getStatusCode() . PHP_EOL . 'Content: ' . $response->getContent() ?: '[none]',
                $response->getStatusCode(),
                $exception
            );
        }
    }
}
