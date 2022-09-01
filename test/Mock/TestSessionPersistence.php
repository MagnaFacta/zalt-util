<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Mock;

use Mezzio\Session\SessionPersistenceInterface;
use PHPUnit\Framework\TestCase;

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @since      Class available since version 1.0
 */
class TestSessionPersistence extends TestCase
{
    public function testCreateSessionCache()
    {
        $cache = SimpleCacheSessionPersistenceFactory::createWithoutServiceManager();
        
        $this->assertInstanceOf(SessionPersistenceInterface::class, $cache);
    }
}