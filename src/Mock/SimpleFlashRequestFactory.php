<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Mock;

use GuzzleHttp\Psr7\ServerRequest;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Session\Cache\CacheSessionPersistence;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Session\SessionPersistenceInterface;

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @since      Class available since version 1.0
 */
class SimpleFlashRequestFactory implements InvokeWithoutServiceManager
{
    static public function createWithoutServiceManager(string $url = '/')
    {
        $request = new ServerRequest('GET', $url);
        
        $smWare = new SessionMiddleware(
            SimpleCacheSessionPersistenceFactory::createWithoutServiceManager()
        );
        $handler  = SimpleRequestHandler::getInstance();
        $response = $smWare->process($request, $handler);
        
        $flWare = new FlashMessageMiddleware();
        $flWare->process($handler->getRequest(), $handler);
        
        return $handler->getRequest();
    }
}