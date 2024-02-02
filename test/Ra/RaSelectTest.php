<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Ra
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Ra;

use PHPUnit\Framework\TestCase;

/**
 * @package    Zalt
 * @subpackage Ra
 * @since      Class available since version 1.0
 */
class RaSelectTest extends TestCase
{
    public static function provideKeyTests()
    {
        $main = [
            'a' => ['b' => 'd', 'c' => ['b' => 'e', 'd' => 'y']],
            'b' => 'x',
            'c' => ['c' => 'd', 'd' => ['b' => 'd']],
            'd' => ['c' => 'd', 'd' => ['c' => 'e']],
        ];

        return [
            'test b' => [$main, 'b', ['d', 'e', 'x', 'd']],
            'test c' => [$main, 'c', [['b' => 'e', 'd' => 'y'], ['c' => 'd', 'd' => ['b' => 'd']], 'd', 'e']],
            'test d' => [$main, 'd', ['y', ['b' => 'd'], ['c' => 'd', 'd' => ['c' => 'e']]]],
        ];
    }

    public static function provideKeysTests()
    {
        $main = [
            'a' => ['b' => 'd', 'c' => ['b' => 'e', 'd' => 'y']],
            'b' => 'x',
            'c' => ['c' => 'd', 'd' => ['b' => 'd']],
            'd' => ['c' => 'd', 'd' => ['c' => 'e']],
        ];

        return [
            'test b' => [$main, ['b'], ['d', 'e', 'x', 'd']],
            'test c' => [$main, ['b', 'c'], ['d', ['b' => 'e', 'd' => 'y'], 'x', ['c' => 'd', 'd' => ['b' => 'd']], 'd', 'e']],
            'test d' => [$main, ['a', 'b', 'c', 'd'], array_values($main)],
        ];
    }

    /**
     * @dataProvider provideKeyTests
     */
    public function testKey(array $input, string $key, array $output): void
    {
        $this->assertEquals($output, RaSelect::forKey($input, $key));
    }

    /**
     * @dataProvider provideKeysTests
     */
    public function testKeys(array $input, array $keys, array $output): void
    {
        $this->assertEquals($output, RaSelect::forKeys($input, $keys));
    }
}