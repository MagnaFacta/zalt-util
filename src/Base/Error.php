<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Base
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Base;

/**
 * @package    Zalt
 * @subpackage Base
 * @since      Class available since version 1.0
 */
class Error
{
    /**
     * Get the string message
     *
     * @param string $defaultMessage Optional default message
     * @return string
     */
    public static function getLastPhpErrorMessage(?string $defaultMessage = null): ?string
    {
        $err = error_get_last();

        if (isset($err['message'])) {
            $needle = '>]:';
            $p      = strpos($err['message'], $needle);
            if (false === $p) {
                $err = $err['message'];
            } else {
                $err = trim(substr($err['message'], $p + strlen($needle)));
            }

            if ('No error' !== $err) {
                return $err;
            }
        }

        return $defaultMessage;
    }
}