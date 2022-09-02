<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Base;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Zalt\Mock\SimpleFlashRequestFactory;
use Zalt\Mock\SimpleServiceManager;

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @since      Class available since version 1.0
 */
class BaseUrlFactoryTest extends TestCase
{
    public function baseExtractionProvider()
    {
        return [
            ['http://localhost/base/index.php', '/base'],
            ['https://secrect:password@localhost/base/index.php?abc=xyz', '/base'],
            ['http://localhost/base/', '/base'],
            ['https://localhost/base?abc=xyz', '/base'],
            ['/base/abc/xyz', '/base/abc/xyz'],
            ['/base?abc=xyz', '/base'],
            ['http://localhost/base/sub.x/whatever.php?abc=xyz', '/base/sub.x'],
            ['ftp://localhost/index.php', ''],
            ['http://localhost/index.php?abc=xyz', ''],
            ['http://localhost', ''],
            ['http://localhost/?abc=xyz', ''],
            ['', ''],
            ];
    }

    /**
     * @dataProvider baseExtractionProvider 
     */
    public function testBaseExtraction(string $inputUrl, string $expected)
    {
        $classes = [
            ServerRequestInterface::class => SimpleFlashRequestFactory::createWithoutServiceManager($inputUrl),
        ];
        $sm = new SimpleServiceManager($classes);

        $buf = new BaseUrlFactory();
        $baseurl = $buf($sm);
        
        $this->assertEquals($expected, $baseurl->getBaseUrl());
    }
}