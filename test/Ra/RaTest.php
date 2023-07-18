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
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 *
 * @package    Zalt
 * @subpackage Ra
 * @since      Class available since version 1.0
 */
class RaTest extends TestCase
{
    public static function fromStd($object)
    {
        return get_object_vars($object);
    }

    public static function isMultiDimensionalDataProvider()
    {
        return [
            [['a', ['b']], true],
            [['a', 'b', []], true],
            [['a', 'b'], false],
        ];
    }

    /**
     * @dataProvider isMultiDimensionalDataProvider
     */
    public function testIsMultiDimensional($array, $test): void
    {
        $this->assertEquals($test, Ra::isMultiDimensional($array));
    }
    
    public function testToArray(): void
    {
        $this->assertIsArray(Ra::to(['a', 'b', 'c']));

        $this->assertEmpty(Ra::to('a', Ra::RELAXED));
        $this->assertIsArray(Ra::to('a', Ra::RELAXED));

        // In (default) strict mode we throw an exception instead
        $this->expectException(Exception::class);
        Ra::to('a');
    }
    
    public function testToConverter(): void
    {
        $obj  = new stdClass();
        $obj->a = 'b';
        $obj->c = 'd';

        // Cannot be converted
        $this->assertEmpty(Ra::to($obj, Ra::RELAXED));

        $conv = Ra::getToArrayConverter();
        $conv->add('stdClass', RaTest::class . '::fromStd');

        // Yes we can!
        $res = Ra::to($obj);
        $this->assertEquals(['a' => 'b', 'c' => 'd'], $res);
    }
    
    public function testToObjects(): void
    {
        $array = ['a', 'b'];
        
        $obj = new ArrayObject(['a', 'b']);
        $res = Ra::to($obj);
        $this->assertIsNotArray($obj);  // ArrayObject is not seen as array
        $this->assertIsObject($obj);
        $this->assertIsArray($res);
        $this->assertIsNotObject($res);
        $this->assertEquals(['a', 'b'], $res);

        $iter = $obj->getIterator();
        $res  = Ra::to($iter);
        $this->assertIsNotArray($obj);  // Iterator is not seen as array
        $this->assertIsObject($obj);
        $this->assertIsArray($res);
        $this->assertIsNotObject($res);
        $this->assertEquals(['a', 'b'], $res);
    }
}