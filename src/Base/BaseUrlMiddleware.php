<?php

namespace Zalt\Base;

use Mezzio\Helper\UrlHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BaseUrlMiddleware implements MiddlewareInterface
{

    public function __construct(private BaseUrlFinder $baseUrlFinder, private UrlHelper $urlHelper)
    {}

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $baseUrl = $this->baseUrlFinder->findBaseUrl($request);

        $this->urlHelper->setBasePath($baseUrl->getBaseUrl());

        //$request = $request->withAttribute(BaseUrl::class, $baseUrl);

        return $handler->handle($request);
    }

}