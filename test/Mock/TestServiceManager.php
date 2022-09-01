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
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @since      Class available since version 1.0
 */
class TestServiceManager extends TestCase
{
    public function testSimpleServiceManager()
    {
        $config = [
            ServerRequestInterface::class => SimpleFlashRequestFactory::createWithoutServiceManager('http://localhost/index.php'),
            TranslatorInterface::class => new PotemkinTranslator(),
        ];
        
        $sm = new SimpleServiceManager($config);
        
        $this->assertInstanceOf(ContainerInterface::class, $sm);
        
        foreach ($config as $class => $object) {
            $this->assertTrue($sm->has($class));
            $this->assertInstanceOf($class, $sm->get($class));
        }
        
        $this->assertFalse($sm->has(self::class));
        $this->expectException(NotFoundExceptionInterface::class);
        $sm->get(self::class);
    }
}