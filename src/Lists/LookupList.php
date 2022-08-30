<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Lists
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Lists;

/**
 * Return a value (the kind is up to the user) using a scalar key.
 *
 * The advantages to using e.g. a standard array object is that both the
 * key type and the search algorithm can be customized in each child class.
 *
 * @package    Zalt
 * @subpackage Lists
 * @since      Class available since version 1.0
 */
class LookupList
{
    protected array $_elements;

    public function __construct(array $initialList = null)
    {
        $this->set((array) $initialList);
    }

    /**
     * Function triggered when the underlying lookup array has changed.
     *
     * This function exists to allow overloading in subclasses.
     *
     * @return void
     */
    protected function _changed(): void
    {  }

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
        if (array_key_exists($key, $this->_elements)) {
            return $this->_elements[$key];
        } else {
            return $default;
        }
    }

    public function add($key, $result = null): LookupList
    {
        if (is_array($key)) {
            $this->merge($key);
        } else {
            $this->set($key, $result);
        }
        return $this;
    }

    public function get($key = null, $default = null): mixed
    {
        if (null === $key) {
            return $this->_elements;
        } else {
            return $this->_getItem($key, $default);
        }
    }

    public function merge(array $mergeList): LookupList
    {
        $this->_elements = array_merge($this->_elements, $mergeList);
        $this->_changed();
        return $this;
    }

    public function remove($key): LookupList
    {
        if (is_array($key)) {
            foreach ($key as $subkey) {
                unset($this->_elements[$subkey]);
            }
        } else {
            unset($this->_elements[$key]);
        }
        $this->_changed();
        return $this;
    }

    public function set(mixed $key, mixed $result = null): LookupList
    {
        if (is_array($key)) {
            $this->_elements = $key;
        } else {
            $this->_elements[$key] = $result;
        }
        $this->_changed();
        return $this;
    }
}