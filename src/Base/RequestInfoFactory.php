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
        $routeMatchedParams = [];

        $path = $request->getUri()->getPath();
        if (pathInfo($path, PATHINFO_EXTENSION)) {
            $baseUrl = dirname($path);
        } else {
            $baseUrl = $path;
        }
        // return ($this->getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest')
        
        $routeResult = $request->getAttribute(RouteResult::class);
        if ($routeResult instanceof RouteResult) {
            $routeMatchedParams = $routeResult->getMatchedParams();
            
            $route = $routeResult->getMatchedRoute();
            if ($route instanceof Route) {
                $options = $route->getOptions();

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
            rtrim($baseUrl, '/\\') ?: '/',
            'POST' == $request->getMethod(),
            $routeMatchedParams,
            $request->getParsedBody() ?: [],
            $request->getQueryParams()
        );


        return $requestInfo;
    }
}