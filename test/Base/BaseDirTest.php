<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Base
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Base;

/**
 * @package    Zalt
 * @subpackage Base
 * @since      Class available since version 1.0
 */
class BaseDirTest extends \PHPUnit\Framework\TestCase
{
    public static function globalsProvider(): array
    {
        return [
            'nobase' => [['SCRIPT_NAME' => '/index.php'], '/'],
            'base' => [['SCRIPT_NAME' => '/base/index.php'], '/base'],
            'sub' => [['SCRIPT_NAME' => '/base/sub/index.php'], '/base/sub'],
            'subtxt' => [['SCRIPT_NAME' => '/base/sub.txt/index.php'], '/base/sub.txt'],
            'base2' => [['PHP_SELF' => '/base/index.php'], '/base'],
            'nobase2' => [['SCRIPT_NAME' => '/index.php', 'PHP_SELF' => '/base/index.php'], '/'],
            'base3' => [['PHP_SELF' => '/index.php', 'SCRIPT_NAME' => '/base/index.php'], '/base'],
            'nobase3' => [['SCRIPT_NAME' => null, 'PHP_SELF' => null, 'ORIG_SCRIPT_NAME' => '/xyz.html'], '/'],
            'empty' => [[], '/'],
        ];
    }

    public static function setProvider(): array
    {
        return [
            ['/', ''],
            ['/base', '/base'],
            ['/base/', '/base'],
            ['base', '/base'],
            ['base/', '/base'],
            ['base/index.php', '/base/index.php'],
        ];
    }

    /**
     * @param array $globals
     * @param string $expected
     * @return void
     * @dataProvider globalsProvider
     */
    public function testFindGlobal(array $globals, string $expected): void
    {
        $this->assertEquals($expected, BaseDir::findBaseDir($globals, true));
    }

    /**
     * @param string $input
     * @param string $expected
     * @return void
     * @dataProvider setProvider
     */
    public function testSetDir(string $input, string $expected): void
    {
        BaseDir::setBaseDir($input);
        $this->assertEquals($expected, BaseDir::getBaseDir());
    }
}