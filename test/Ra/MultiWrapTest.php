<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Ra
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Ra;

use ArrayObject;
use PHPUnit\Framework\TestCase;

/**
 *
 * @package    Zalt
 * @subpackage Ra
 * @since      Class available since version 1.0
 */
class MultiWrapTest extends TestCase
{
    public static function contentProvider()
    {
        $array1 = ['a' => new \stdClass(), 'b' => new \stdClass(), 'c' => new \stdClass()];
        $array2 = ['a' => new \stdClass(), 'b' => new \stdClass(), 'c' => new \stdClass()];

        return [
            [$array1, new MultiWrapper($array1)],
            [$array2, new MultiWrapper(new ArrayObject($array2))],
        ];
    }

    /**
     * @dataProvider contentProvider
     */
    public function testWithTraverable(array $array, MultiWrapper $multi): void
    {
        $this->assertFalse(isset($multi->a));

        // @phpstan-ignore property.notFound
        $result = $multi->a;
        $this->assertEmpty($result);
        foreach ($array as $key => $object) {
            $this->assertFalse(isset($object->a));
        }

        // @phpstan-ignore property.notFound
        $multi->a = 'b';
        $result = $multi->a;
        $this->assertCount(3, $result);
        foreach ($array as $key => $object) {
            $this->assertEquals('b', $object->a);
        }
    }
}