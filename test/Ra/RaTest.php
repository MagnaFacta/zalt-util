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

    /**
     * Data provider for the insertAfter method tests.
     */
    public static function insertAfterDataProvider(): array
    {
        return [
            'key-exists-without-new-key' => [
                'input' => ['a' => 1, 'b' => 2, 'c' => 3],
                'afterKey' => 'b',
                'value' => 4,
                'keyValue' => null,
                'expected' => ['a' => 1, 'b' => 2, 0 => 4, 'c' => 3],
            ],
            'key-exists-with-new-key' => [
                'input' => ['a' => 1, 'b' => 2, 'c' => 3],
                'afterKey' => 'b',
                'value' => 4,
                'keyValue' => 'd',
                'expected' => ['a' => 1, 'b' => 2, 0 => 4, 'c' => 3],
            ],
            'key-does-not-exist-without-new-key' => [
                'input' => ['a' => 1, 'b' => 2, 'c' => 3],
                'afterKey' => 'x',
                'value' => 4,
                'keyValue' => null,
                'expected' => ['a' => 1, 'b' => 2, 'c' => 3, 3 => 4],
            ],
            'key-does-not-exist-with-new-key' => [
                'input' => ['a' => 1, 'b' => 2, 'c' => 3],
                'afterKey' => 'x',
                'value' => 4,
                'keyValue' => 'd',
                'expected' => ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
            ],
            'empty-array' => [
                'input' => [],
                'afterKey' => 'a',
                'value' => 1,
                'keyValue' => 'b',
                'expected' => ['b' => 1],
            ],
        ];
    }

    /**
     * @dataProvider insertAfterDataProvider
     */
    public function testInsertAfter(array $input, $afterKey, $value, $keyValue, array $expected): void
    {
        $result = Ra::insertAfter($input, $afterKey, $value, $keyValue);
        $this->assertEquals(array_values($expected), array_values($result));
        $this->assertEquals(array_keys($expected), array_keys($result));
    }

    /**
     * Data provider for the insertBefore method tests.
     */
    public static function insertBeforeDataProvider(): array
    {
        return [
            'key-exists-without-new-key' => [
                'input' => ['a' => 1, 'b' => 2, 'c' => 3],
                'beforeKey' => 'b',
                'value' => 4,
                'keyValue' => null,
                'expected' => ['a' => 1,  0 => 4, 'b' => 2,'c' => 3],
            ],
            'key-exists-with-new-key' => [
                'input' => ['a' => 1, 'b' => 2, 'c' => 3],
                'beforeKey' => 'b',
                'value' => 4,
                'keyValue' => 'd',
                'expected' => ['a' => 1, 0 => 4, 'b' => 2, 'c' => 3],
            ],
            'key-does-not-exist-without-new-key' => [
                'input' => ['a' => 1, 'b' => 2, 'c' => 3],
                'beforeKey' => 'x',
                'value' => 4,
                'keyValue' => null,
                'expected' => [3 => 4, 'a' => 1, 'b' => 2, 'c' => 3],
            ],
            'key-does-not-exist-with-new-key' => [
                'input' => ['a' => 1, 'b' => 2, 'c' => 3],
                'beforeKey' => 'x',
                'value' => 4,
                'keyValue' => 'd',
                'expected' => ['d' => 4, 'a' => 1, 'b' => 2, 'c' => 3],
            ],
            'empty-array' => [
                'input' => [],
                'beforeKey' => 'a',
                'value' => 1,
                'keyValue' => 'b',
                'expected' => ['b' => 1],
            ],
        ];
    }

    /**
     * @dataProvider insertBeforeDataProvider
     */
    public function testInsertBefore(array $input, $beforeKey, $value, $keyValue, array $expected): void
    {
        $result = Ra::insertBefore($input, $beforeKey, $value, $keyValue);
        $this->assertEquals(array_values($expected), array_values($result));
        $this->assertEquals(array_keys($expected), array_keys($result));
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

    /**
     * Data provider for the keySplit method tests.
     */
    public static function keySplitDataProvider(): array
    {
        return [
            'empty-array' => [[], [[], []]],
            'only-integer-keys' => [[0 => 'a', 1 => 'b'], [[0 => 'a', 1 => 'b'], []]],
            'only-string-keys' => [['key1' => 'a', 'key2' => 'b'], [[], ['key1' => 'a', 'key2' => 'b']]],
            'mixed-keys' => [[0 => 'a', 'key2' => 'b', 1 => 'c', 'key4' => 'd'],
                [[0 => 'a', 1 => 'c'], ['key2' => 'b', 'key4' => 'd']]],
            'single-integer-key' => [[1 => 'value'], [[1 => 'value'], []]],
            'single-string-key' => [['key' => 'value'], [[], ['key' => 'value']]],
            'nested-arrays' => [[0 => [1, 2], 'key' => ['a', 'b']],
                [[0 => [1, 2]], ['key' => ['a', 'b']]]],
        ];
    }

    /**
     * @dataProvider keySplitDataProvider
     */
    public function testKeySplit(array $input, array $expected): void
    {
        $result = Ra::keySplit($input);
        $this->assertSame($expected, $result);
    }

    public static function pairsDataProvider(): array
    {
        return [
            'odd-number-of-args' => [['key1', 'value1', 'key2'], 0, ['key1' => 'value1', 'key2' => null]],
            'even-number-of-args' => [['key1', 'value1', 'key2', 'value2'], 0, ['key1' => 'value1', 'key2' => 'value2']],
            'skip-args' => [['skip', 'key1', 'value1', 'key2', 'value2'], 1, ['key1' => 'value1', 'key2' => 'value2']],
            'skip-2args' => [['skip', 'skip2', 'key1', 'value1', 'key2', 'value2'], 2, ['key1' => 'value1', 'key2' => 'value2']],
            'single-array-arg' => [[['key1' => 'value1', 'key2' => 'value2']], 0, ['key1' => 'value1', 'key2' => 'value2']],
            'empty-array' => [[], 0, []],
            'array-object' => [[new ArrayObject(['key1' => 'value1', 'key2' => 'value2'])], 0, ['key1' => 'value1', 'key2' => 'value2']],
        ];
    }

    /**
     * @dataProvider pairsDataProvider
     */
    public function testPairs(array $args, int $skip, array $expected): void
    {
        $this->assertSame($expected, Ra::pairs($args, $skip));
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
        $res = Ra::to($iter);
        $this->assertIsNotArray($obj);  // Iterator is not seen as array
        $this->assertIsObject($obj);
        $this->assertIsArray($res);
        $this->assertIsNotObject($res);
        $this->assertEquals(['a', 'b'], $res);

        $this->expectException(\Exception::class);
        $res = Ra::to(new \Exception(), Ra::STRICT);
    }
}