<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Ra
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Ra;

use Exception;
use Zalt\Lists\ClassList;

/**
 * The Ra class contains static array processing functions that are used to give PHP/Zend some
 * Python and Haskell like parameter processing functionality, though a lot of that is already
 * possible in PHP 8.1.
 *
 * Ra class: pronouce "array" except on 19 september, then it is "ahrrray".
 *
 * The functions are:<ol>
 * <li>Ra::args    => Python faking</li>
 * <li>Ra::flatten => flatten an array renumbering keys</li>
 * <li>Ra::pairs   => the parameters represent name => value pairs</li></ol>
 *
 * @package    Zalt
 * @subpackage Ra
 * @since      Class available since version 1.0
 */
class Ra
{
    const RELAXED = 0;
    const STRICT  = 1;

    /**
     * Original list of class converters to convert objects to array using one
     * of the objects methods to create one.
     *
     * @var array
     */
    private static $_initialToArrayList = array(
        'ArrayObject'                      => 'getArrayCopy',
        'Zalt\\Late\\LazyInterface'        => 'Zalt\\Late\\Late::rise',
        'Zalt\\Late\\RepeatableInterface'  => '__current',

        // Last function to try
        'Traversable' => 'iterator_to_array'
        );

    /**
     * A class list with function that convert data to an array
     *
     * @var \Zalt\Lists\ClassList
     */
    private static ClassList $_toArrayConverter;

    /**
     * Maximum number of steps to convert to an array (because of loops)
     *
     * @var int
     */
    public static int $toArrayConverterLoopLimit = 10;

    /**
     * Add the key field to the values in the input array.
     *
     * Input values that are an array get only the key added as field,
     * input values that are scalar are changed into array($keyField -> key, $valueField => value).
     *
     * @ param array $input Yhe input array / iterator / etc...
     * @ param string $keyField The string to use as key for the key field
     * @ param string $valueField The string to use as key for the value field when the value is not an array
     * /
    public static function addKey(array $input, $keyField = 'key', $valueField = 'value')
    {
        $output = array();

        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $output[$key] = $value;
                $output[$key][$keyField] = $key;
            } else {
                $output[$key][$keyField]   = $key;
                $output[$key][$valueField] = $value;
            }
        }

        return $output;
    }

    /**
     * The args() function makes position independent argument passing possible.
     *
     * FLATTENING THE INPUT
     *
     * The input is usually just the output of func_get_args(). This array is flattened
     * like this:
     * <code>
     * Ra::args(
     *  array(0 => array(0 => 'f', 1 => array('o' => '0', 0 => 'b')), 1 => array('a' => array('r' => 'r'))));
     * =>
     *  array(0 => 'f', 'o' => '0', 1 => 'b', 'a' => array('r' => 'r'))
     * </code>
     * Watch the last array() statement: when an item is an array has a numeric key it is flattened,
     * when the item is an array but has a string key it is kept as is.
     *
     *
     * If you assign a name twice, the last value is used:
     * <code>
     * Ra::args(
     *  array(array('a' => 'b'), array('a' => 'c'));
     * =>
     *  array('a' => 'c');
     * </code>
     *
     * SKIPPING ARGUMENTS
     *
     * When the first X arguments passed to a function are fixed, you can skip the flattening of the
     * items by specifiying a numeric $skipOrName value.
     * <code>
     * Ra::args(
     *  array(0 => array(0 => 'f', 1 => array('o' => '0', 0 => 'b')), 1 => array('a' => array('r' => 'r'))),
     *  1);
     * =>
     * array('a' => array('r' => 'r'))
     * </code>
     *
     * NAMING ARGUMENTS
     *
     * When you want flexibility in passing arguments a better option than using fixed arguments
     * is using named arguments. With array('foo', 'bar') as $skipOrName parameter the previous
     * example output becomes:
     * <code>
     * Ra::args(
     *  array(0 => array(0 => 'f', 1 => array('o' => '0', 0 => 'b')), 1 => array('a' => array('r' => 'r'))),
     *  array('foo', 'bar'));
     * =>
     *  array('foo' => 'f', 'o' => '0', 'bar' => 'b', 'a' => array('r' => 'r'))
     * </code>
     * The names are assigned only to numeric array elements and are assigned depth first. On the
     * other hand the args function does not care about the output array positions so the actual
     * output has a different order:
     * <code>
     * array('o' => '0', 'a' => array('r' => 'r'), 'foo' => 'f', 'bar' => 'b')
     * </code>
     *
     * Using the $skipOrName array('a', 'c', 'o') the same example returns:
     * <code>
     * Ra::args(
     *  array(0 => array(0 => 'f', 1 => array('o' => '0', 0 => 'b')), 1 => array('a' => array('r' => 'r'))),
     *  array('a', 'c', 'o'));
     * =>
     *  array('c' => 'f', 'o' => '0', 1 => 'b', 'a' => array('r' => 'r'))
     * </code>
     * As both 'a' and 'o' exist already they are not reassigned, independent of the position
     * where they were defined in the original input.
     *
     * As 'c' is the only parameter not assigned it is assigned to the first numeric parameter
     * value.
     *
     *
     * TYPING NAMED ARGUMENTS
     *
     * args() also supports class-typed arguments. The $skipOrName parameter then uses the
     * name of the parameter as the array key and the class or interface name as the value:
     * <code>
     * Ra::args(
     *  array(new \Zend_DB_Select(), array('a', 'b', new \Zend_Foo()))),
     *  array('foo' => 'Zend_Foo', 'bar', 'foobar' => 'Zend_Db_Select'));
     * =>
     *  array('foo' => new \Zend_Foo(), 'bar' => 'a', 'foobar' => new \Zend_Db_Select(), 0 => 'b');
     * </code>
     * Of course the actual order is not important, as is the actual number assigned to the last
     * parameter value.
     *
     * Assignment is depth first. Mind you, assignment is name first, instanceof second as long
     * as the $mode = Ra::RELAXED. If the name does not correspond to the specified type
     * it is still assigned. Also the assignment order is again depth first:
     * <code>
     * Ra::args(
     *  array(new \Zend_Foo(1), array('a', 'b', new \Zend_Foo(2)), array('foobar' => 'x')),
     *  array('foo' => 'Zend_Foo', 'bar' => 'Zend_Foo', 'foobar' => 'Zend_Db_Select'));
     * =>
     *  array('foo' => new \Zend_Foo(1), 'bar' => new \Zend_Foo(2), 'foobar' => 'x', 0 => 'a', 1 => 'b');
     * </code>
     *
     *
     * OTHER TYPE OPTIONS
     *
     * Apart from class names you can also use is_*() functions to test for a type. E.g. is_string() or
     * is_boolean(). You can also write your own is_whatever() function.
     *
     * You can assign multiple types as an array. The array will search all the arguments first for the
     * first type, then the second, etc..
     *
     * The next example will get the first passed compatible Zend element (which your code can use to get
     * the id of) or else the first available string parameter.
     * <code>
     *  array('id' => array('Zend_Form_Element', 'Zend_Form_DisplayGroup', 'Zend_Form', 'is_string'));
     * </code>
     *
     * ADDING DEFAULTS
     *
     * func_get_args() returns the passed arguments without any specified default values. When your
     * function has defaults you have to add them as an 'name' => 'value' array as the third argument.
     *
     * So the example:
     * <code>
     * $args = Ra::args(func_get_args(), array('class1',  'class2'), array('class1' => 'odd',  'class2' => 'even'));
     * </code>
     * Will return this for the inputs:
     * <code>
     * array() = array('class1' => 'odd',  'class2' => 'even');
     * array(null) = array('class1' => null,  'class2' => 'even');
     * array('r1', 'r2') = array('class1' => 'r1',  'class2' => 'r2');
     * array('r1', 'r2', 'r3') = array('class1' => 'r1',  'class2' => 'r2', 0 => 'r3');
     * </code>
     *
     * @param array $args       An array containing the arguments to process (usually func_get_args() output)
     * @param mixed $skipOrName If numeric the number of arguments in $args to leave alone, otherwise the names of numbered
     *                          elements. Class names can also be specified.
     * @param array $defaults   An array of argument name => default_value pairs.
     * @param int $mode     The $skipOrName types are only used as hints or must be strictly adhered to.
     * @return array Flattened array containing the arguments.
     */
    public static function args(array $args, $skipOrName = 0, $defaults = array(), $mode = self::RELAXED)
    {
        if ($skipOrName) {
            if (is_integer($skipOrName)) {
                // TEST RESULT
                //
                // As expected array_slice() is an order of magnitude faster than
                // using array_shift() repeatedly. It is even faster to use
                // array_slice() repeatedly than to use array_shift().
                $args = array_slice($args, $skipOrName);

            } else {
                $laxTypes = (self::RELAXED === $mode);

                if (is_array($skipOrName)) {
                    $names = $skipOrName;
                } else {
                    $names = array($skipOrName);
                }

                // Assign numbered array items to the names specified (if any)
                foreach ($names as $n1 => $n2) {
                    // The current element is always the first in the args array,
                    // as long as the corresponding key is numeric.
                    //
                    // When the "supply" of numeric keys is finished we have processed
                    // all the keys that were passed.
                    reset($args);
                    $current = key($args);
                    if (! is_int($current)) {
                        break;
                    }

                    // The parameter type
                    if (is_int($n1)) {
                        $ntype = null;
                        $name  = $n2;
                    } else {
                        $ntype = $n2;
                        $name  = $n1;
                    }

                    if (is_array($ntype)) { // Algebraic type!
                        foreach ($ntype as $stype) {
                            if (self::argsSearchKey($name, $stype, $args, $laxTypes)) {
                                break;
                            }
                            $ntype = $stype;  // Allows using null as a last type
                        }
                    } else {
                        self::argsSearchKey($name, $ntype, $args, $laxTypes);
                    }

                    // 1: Not yet set && 2: lax types used
                    if ((! isset($args[$name])) &&
                        ($laxTypes || (null === $ntype)) &&
                        array_key_exists($current, $args)) {

                        $args[$name] = $args[$current];
                        unset($args[$current]);
                    }
                }
            }
        }

        $output = array();

        if ($args) {
            // flatten out all sub-arrays with a numeric key
            self::argsRenumber($args, $output);
        }

        if ($defaults) {
            // Add array with default values/
            $output = $output + $defaults;
        }

        return $output;
    }

    private static function argsRenumber(array $input, array &$output)
    {
        foreach ($input as $key => $value) {
            if (is_int($key)) {
                if (is_array($value)) {
                    self::argsRenumber($value, $output);
                } else {
                    $output[] = $value;
                }
            } else {
                $output[$key] = $value;
            }
        }
    }

    private static function argsSearchKey($needle, $needleType, array &$haystack, $laxTypes)
    {
        foreach ($haystack as $key => $value) {
            if (is_int($key)) {
                // Check higher up in array
                if (is_array($value) && self::argsSearchKey($needle, $needleType, $value, $laxTypes)) {
                    // Give the value the correct array key
                    //
                    // This bubbles the array value up to the current $haystack level
                    $haystack[$needle] = $value[$needle];
                    unset($haystack[$key][$needle]);

                    // Remove array if no longer in use
                    if (count($haystack[$key]) == 0) {
                        unset($haystack[$key]);
                    }
                    return true;
                }
            } elseif ($laxTypes && ($needle == $key)) {
                return true;
            }
            if ($needleType) {
                // Check for type os check for is_etc... function
                $isType = ($value instanceof $needleType) ||
                    ((substr($needleType, 0, 3) == 'is_') && function_exists($needleType) && $needleType($value));

                if ($isType) {
                    // Give the value the correct array key
                    $haystack[$needle] = $value;
                    unset($haystack[$key]);
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Flattens an array recursively.
     *
     * All keys are removed and items are added depth-first to the output array.
     *
     * @param array $input
     * @return array
     */
    public static function flatten(array $input)
    {
        $output = array();
        self::flattenSub($input, $output);
        return $output;
    }

    private static function flattenSub(array $input, array &$output)
    {
        foreach ($input as $value) {
            if (is_array($value)) {
                self::flattenSub($value, $output);
            } else {
                $output[] = $value;
            }
        }
    }

    /**
     * Get or create the current to ArrayConverter
     *
     * @return ClassList or something that can be used as input to create one
     */
    public static function getToArrayConverter(): ClassList
    {
        if (! isset(self::$_toArrayConverter)) {
            self::setToArrayConverter(self::$_initialToArrayList);
        }

        return self::$_toArrayConverter;
    }

    /**
     * Returns true if the $object either is an array or can be converted to an array.
     *
     * @param mixed $object
     * @return bool
     */
    public static function is($object): bool
    {
        if (is_array($object)) {
            return true;
        }

        return (boolean) self::getToArrayConverter()->get($object);
    }

    /**
     * @param array $value
     * @return bool True when there is an array in the value
     */
    public static function isMultiDimensional(array $value)
    {
        foreach ($value as $val) {
            if (is_array($val)) {
                return true;
            }
        }
        
        return false;        
    }
    
    /**
     * This functions splits an array into two arrays, one containing
     * the integer keys and one containing the string keys and returns
     * an array containing first the integer key array and then the
     * string key array.
     *
     * @param array $arg The input array
     * @return array array(integer_keys, string_keys)
     */
    public static function keySplit(array $arg)
    {
        $nums    = array();
        $strings = array();

        foreach ($arg as $key => $value) {
            if (is_integer($key)) {
                $nums[$key] = $value;
            } else {
                $strings[$key] = $value;
            }
        }

        return array($nums, $strings);
    }

    /**
     * A function that transforms an array in the form key1, value1, key2, value2 into array(key1 => value1, key2 => value2).
     *
     * When the $args array contains only a single sub array, then this value is assumed to be the return value. This allows
     * functions using pairs() to process their values to accept both:
     *    f1('key1', 'value1', 'key2', 'value2')
     *  and:
     *    $a = array('key1' => 'value1', 'key2' => 'value2');
     *    f1($a)
     *
     * @param array $args Usually func_get_args() from the calling function.
     * @param int $skip The number of items to skip before stating processing
     * @return array
     */
    public static function pairs(array $args, $skip = 0): array
    {
        $count = count($args);

        // When only one array element was passed that is the return value.
        if ($count == $skip + 1) {
            $arg = $args[$skip];
            if (is_array($arg)) {
                return $arg;
            }
            if (is_object($arg)) {
                return self::to($arg);
            }
        }

        // When odd number of items: add null value at end to even out values.
        if (1 == (($count - $skip) % 2)) {
            $args[] = null;
        }

        $pairs = array();
        for ($i = $skip; $i < $count; $i += 2) {
            $pairs[$args[$i]] = $args[$i + 1];
        }

        return $pairs;
    }

    /**
     * Set the current to ArrayConverter
     *
     * @param mixed $converter ClassList or something that can be used as input to create one
     */
    public static function setToArrayConverter($converter)
    {
        if ($converter instanceof ClassList) {
            self::$_toArrayConverter = $converter;
        } elseif (is_array($converter)) {
            self::$_toArrayConverter = new ClassList($converter);
        }
    }

    /**
     * Convert object types to an array
     *
     * @param mixed $object
     * @param int $mode RELAXED OR STRICT
     * @return array
     * @throws Exception
     */
    public static function to($object, $mode = self::STRICT): array
    {
        // Allow type chaining => Lazy => Config => array
        $i = 0;
        $converter = self::getToArrayConverter();
        while (is_object($object) && ($function = $converter->get($object))) {

            if (method_exists($object, $function)) {
                $object = call_user_func(array($object, $function));
            } else {
                $object = call_user_func($function, $object);
            }

            if (++$i > self::$toArrayConverterLoopLimit) {
                throw new Exception('Object of type ' . get_class($object) . ' results in to many loops in array conversion.');
            }
        }

        if (is_array($object)) {
            return $object;
        }

        if (self::STRICT === $mode) {
            if (is_object($object)) {
                throw new Exception('Object of type ' . get_class($object) . ' could not be converted to array.');
            } else {
                throw new Exception('Item of type ' . gettype($object) . ' could not be converted to array.');
            }
        }

        return [];
    } // */
}

/*
function is_ra_array($value)
{
    return Ra::is($value);
}

// */