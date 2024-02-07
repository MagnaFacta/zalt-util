<?php

namespace Zalt\String;

class Str
{
    /**
     * Convert a string to kebab case.
     */
    public static function kebab(string $value = null): string
    {
        return static::snake($value, '-');
    }

    /**
     * Convert a string to snake case.
     */
    public static function snake(
        string $value = null,
        string $delimiter = '_'
    ): string {
        if (ctype_lower($value) === false) {
            $value = preg_replace('/[\s\\\]+/u', '', ucwords($value));
            $value = preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value);
            $value = static::lower($value);
        }
        return $value;
    }

    /**
     * A UTF-8 safe version of strtolower()
     */
    public static function lower(string $string = null): string
    {
        return mb_strtolower($string ?? '', 'UTF-8');
    }
}