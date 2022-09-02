<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Base;

use Psr\Container\ContainerInterface;

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @since      Class available since version 1.0
 */
class BasicRedirectorFactory
{
    public function __invoke(ContainerInterface $container): BasicRedirector
    {
        return new BasicRedirector();
    }
}