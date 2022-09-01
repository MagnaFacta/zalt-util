<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Mock;

/**
 *
 * @package    Zalt
 * @subpackage Mock
 * @since      Class available since version 1.0
 */
interface InvokeWithoutServiceManager
{
    static public function createWithoutServiceManager();
}