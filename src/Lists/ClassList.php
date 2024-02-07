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
 * Return a value (the kind is up to the user), using either an object
 * or a class name as lookup key.
 *
 * When not finding a direct match, this object checks (first) the parent classes
 * and then the interfaces for a match. Search results are then stored in a cache.
 *
 * @package    Zalt
 * @subpackage Lists
 * @since      Class available since version 1.0
 */
class ClassList extends LookupList
{
    /**
     * Sub classes known to have a mapping
     *
     * @var array
     */
    protected $_subClasses;

    /**
     * Classes not found in this lookup list
     *
     * @var array
     */
    protected $_notSubClasses;

    /**
     * Function triggered when the underlying lookup array has changed.
     *
     * This function exists to allow overloading in subclasses.
     *
     * @return void
     */
    protected function _changed(): void
    {
        // Clean up internal caches
        $this->_subClasses = array();
        $this->_notSubClasses = array();
    }

    /**
     * Item lookup function.
     *
     * This is a separate function to allow overloading by subclasses.
     *
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    protected function _getItem($key, mixed $default = null): mixed
    {
        if (is_object($key)) {
            $class = get_class($key);
        } else {
            $class = $key;
        }

        // Check for simple existence
        if ($result = parent::_getItem($class, $default)) {
            return $result;
        }

        // Check was already found
        if (array_key_exists($class, $this->_subClasses)) {
            return $this->_subClasses[$class];
        }

        // Check was already searched and not found
        if (array_key_exists($class, $this->_notSubClasses)) {
            return $default;
        }

        // Check the parent classes of the object
        $parents = class_parents($key);
        $result = null;
        foreach ($parents as $parentClass) {
            if ($result = parent::_getItem($parentClass, null)) {
                // Add the current class to the cache
                $this->_subClasses[$class] = $result;

                // Add all parents up to the one matching to the cache
                foreach ($parents as $priorParent) {
                    $this->_subClasses[$priorParent] = $result;
                    if ($parentClass === $priorParent) {
                        // Further parents are not automatically in the list
                        break;
                    }
                }
                return $result;
            }
        }

        // Check the interfaces implemented by the object
        $implemented = class_implements($key);
        foreach ($implemented as $interface) {
            if ($result = parent::_getItem($interface, null)) {
                //    Add the current class to the cache
                $this->_subClasses[$class] = $result;
                return $result;
            }
        }

        // Add to the not found cache
        $this->_notSubClasses[$class] = true;

        return $default;
    }
}