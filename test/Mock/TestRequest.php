<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Mock;

use Mezzio\Flash\FlashMessagesInterface;
use Mezzio\Session\LazySession;
use Mezzio\Session\SessionInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @since      Class available since version 1.0
 */
class TestRequest extends TestCase
{
    public function testRequestObject()
    {
        $request = SimpleFlashRequestFactory::createWithoutServiceManager('http://localhost/index.php');
        
        $this->assertInstanceOf(ServerRequestInterface::class, $request);

        $this->assertInstanceOf(LazySession::class, $request->getAttribute('session'));
        $this->assertInstanceOf(LazySession::class, $request->getAttribute(SessionInterface::class));
        $this->assertInstanceOf(FlashMessagesInterface::class, $request->getAttribute('flash'));
    }
}