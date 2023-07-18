<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Mock;

use Mezzio\Router\Exception;
use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @since      Class available since version 1.0
 */
class SimpleRouter implements \Mezzio\Router\RouterInterface
{
    /**
     * @inheritDoc
     */
    public function addRoute(Route $route) : void
    {
    }

    /**
     * @inheritDoc
     */
    public function match(Request $request) : RouteResult
    {
        return RouteResult::fromRouteFailure(null);
    }

    /**
     * @inheritDoc
     */
    public function generateUri(string $name, array $substitutions = [], array $options = []) : string
    {
        return '';
    }
}