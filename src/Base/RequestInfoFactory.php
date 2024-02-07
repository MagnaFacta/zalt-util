<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Base;

use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ServerRequestInterface;

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @since      Class available since version 1.0
 */
class RequestInfoFactory
{
    public static function getMezzioRequestInfo(ServerRequestInterface $request): RequestInfo
    {
        $actionName         = null;
        $controllerName     = null;
        $routeName          = null;
        $routeMatchedParams = [];

        $baseDir = BaseDir::getBaseDir();
        $path    = $request->getUri()->getPath();
        if (str_contains(basename($path), '.')) {
            $path = dirname($path);
        }
        if ($baseDir && str_starts_with($path, $baseDir)) {
            $path = substr($path, strlen($baseDir));
        }

        $routeResult = $request->getAttribute(RouteResult::class);
        if ($routeResult instanceof RouteResult) {
            $routeMatchedParams = $routeResult->getMatchedParams();
            
            $route = $routeResult->getMatchedRoute();
            if ($route instanceof Route) {
                $routeName = $route->getName();
                $options   = $route->getOptions();

                if (isset($options['controller'])) {
                    $controllerName = $options['controller'];
                }
                if (isset($options['action'])) {
                    $actionName = $options['action'];
                }
            }
        }
        
        $requestInfo = new RequestInfo(
            $controllerName, 
            $actionName,
            $routeName,
            $baseDir,
            rtrim($path, '/\\'),
            'POST' == $request->getMethod(),
            $routeMatchedParams,
            $request->getParsedBody() ?: [],
            $request->getQueryParams()
        );


        return $requestInfo;
    }
}