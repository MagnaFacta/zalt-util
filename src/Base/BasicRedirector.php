<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Base;

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @since      Class available since version 1.0
 */
class BasicRedirector implements RedirectorInterface
{
    public function redirect(string $url, int $code = 302): void
    {
        header('Location: ' . $url, true, $code);
    }
}