<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Ra
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Ra;

/**
 * @package    Zalt
 * @subpackage Ra
 * @since      Class available since version 1.0
 */
class RaSelect
{
    /**
     * @param array $input Nested array to search in
     * @param $needle The array key to search values for
     * @return array The values having that key
     */
    public static function forKey(array $input, $needle): array
    {
        $output = [];

        self::forKeyRecursive($output, $input, $needle);

        return $output;
    }

    private static function forKeyRecursive(array &$output, array $input, $needle)
    {
        foreach ($input as $key => $value) {
            if ($key == $needle) {
                $output[] = $value;
            } elseif (is_array($value)) {
                self::forKeyRecursive($output, $value, $needle);
            }
        }
    }

    /**
     * @param array $input Nested array to search in
     * @param array $needles The array keys to search values for
     * @return array The values having that key
     */
    public static function forKeys(array $input, array $needles): array
    {
        $output = [];

        self::forKeysRecursive($output, $input, $needles);

        return $output;
    }

    private static function forKeysRecursive(array &$output, array $input, array $needles)
    {
        foreach ($input as $key => $value) {
            if (in_array($key, $needles)) {
                $output[] = $value;
            } elseif (is_array($value)) {
                self::forKeysRecursive($output, $value, $needles);
            }
        }
    }
}