<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Mock;

use Symfony\Component\Cache\Adapter\NullAdapter;
use Mezzio\Session\Cache\CacheSessionPersistence;

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @since      Class available since version 1.0
 */
class SimpleCacheSessionPersistenceFactory implements InvokeWithoutServiceManager
{
    static public function createWithoutServiceManager()
    {
        return new CacheSessionPersistence(
            new NullAdapter(),
            'PHPSESSION',
            '/',
            'nocache',
            10800,
            null,
            false,
            null,
            false,
            false,
            'Lax'
        );
    }
}