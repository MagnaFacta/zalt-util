<?php

declare(strict_types=1);

/**
 *
 *
 * @package    Zalt
 * @subpackage Ra
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Ra;

use ArrayObject;

/**
 * Simple extension of ArrayObject allowing casting to string with (optionally) a specified glue.
 *
 * @package    Zalt
 * @subpackage Ra
 * @since      Class available since Zalt-util version 1.0
 */
class ArrayString extends ArrayObject
{
    /**
     * The glue to insert between the array pieces when casting to string.
     *
     * @var string
     */
    private string $glue = '';

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return implode($this->getGlue(), $this->getArrayCopy());
    }

    /**
     *
     * @return string
     */
    public function getGlue(): string
    {
        return $this->glue;
    }

    /**
     *
     * @param string $glue The glue to use
     * @return ArrayString
     */
    public function setGlue(string $glue)
    {
        $this->glue = $glue;

        return $this;
    }
}