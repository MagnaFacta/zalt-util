<?php

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 * @license    New BSD License
 */

namespace Zalt\Base;

use PHPUnit\Framework\TestCase;

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @since      Class available since version 1.0
 */
class BaseUrlTest extends TestCase
{
    public static function knownProvider(): array
    {
        return [
            ['base', 'base'],
            ['/base', '/base'],
            ['base/', 'base'],
            ['/base/', '/base'],
            ['\\base', '\\base'],
            ['base\\', 'base'],
            ['/\\base\\/', '/\\base'],
            ['\\/base/\\', '\\/base'],
            ['/base/sub\\', '/base/sub'],
            ['/base/sub', '/base/sub'],
        ];    
    }
    
    public function testEmpty(): void
    {
        $base = new BaseUrl();
        
        $this->assertEmpty($base->getBaseUrl());
        $this->assertEmpty((string) $base);
    }

    /**
     * @dataProvider knownProvider
     */
    public function testKnown($input, $expected): void
    {
        $base = new BaseUrl();
        $base->setBaseUrl($input);
        $this->assertEquals($expected, $base->getBaseUrl());
    }
}