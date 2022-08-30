<?php

declare(strict_types=1);

/**
 *
 *
 * @package    Zalt
 * @subpackage Lists
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Lists;

/**
 * Return a function value using a scalar key.
 *
 * @package    Zalt
 * @subpackage Lists
 * @since      Class available since version 1.0
 */
class FunctionList extends LookupList
{
    /**
     * Item lookup function.
     *
     * This is a separate function to allow overloading by subclasses.
     *
     * @param scalar $key
     * @param mixed $default
     * @return mixed
     */
    protected function _getItem($key, mixed $default = null): mixed
    {
        if (isset($this->_elements[$key])) {
            $function = $this->_elements[$key];

            if (is_callable($function)) {
                return $function;
            } else {
                return $default;
            }
        } else {
            return $default;
        }
    }
}