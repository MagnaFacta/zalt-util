<?php

namespace Zalt\Base;

use Psr\Http\Message\ServerRequestInterface;

class BaseUrlFinder
{
    public function findBaseUrl(ServerRequestInterface $request): BaseUrl
    {
        $baseUrl = new BaseUrl();

        $path = $request->getUri()->getPath();
        if (pathInfo($path, PATHINFO_EXTENSION)) {
            $baseUrl->setBaseUrl(dirname($path));
        } else {
            $baseUrl->setBaseUrl($path);
        }

        return $baseUrl;
    }
}