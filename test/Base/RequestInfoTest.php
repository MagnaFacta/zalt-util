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
use Zalt\Mock\SimpleFlashRequestFactory;

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @since      Class available since version 1.0
 */
class RequestInfoTest extends TestCase
{
    /**
     * @dataProvider urlProvider 
     */
    public function testUrls(string $url, string $base, string $path, $param = null, $value = null): void
    {
        $request = SimpleFlashRequestFactory::createWithoutServiceManager($url);

        BaseDir::setBaseDir($base);
        $requestInfo = RequestInfoFactory::getMezzioRequestInfo($request);
        
        $this->assertEquals(rtrim($base, '\\/'), $requestInfo->getBaseDir());
        $this->assertEquals(rtrim($base . $path, '\\/'), $requestInfo->getBasePath());
        if ($param) {
            $this->assertEquals($value, $requestInfo->getParam($param));
        }
        if ($path) {
            $this->assertEquals($path, $requestInfo->getPath());
        }
    }
    
    public static function urlProvider()
    {
        return [
            ['https://zelt.test.nl/', '/', ''],
            ['https://zelt.test.nl/index.php?x=y', '/', '', 'x', 'y'],
            ['https://localhost/base', '/base', ''],
            ['https://localhost/base', '', '/base'],
            ['https://localhost/base/index.php', '/base', ''],
            ['https://localhost/base/index.php', '', '/base'],
            ['https://localhost/base/?x=y', '/base', '', 'x', 'y'],
            ['https://localhost/basic/?x=y&z=a&b=c', '/basic', '', 'x', 'y'],
            ['https://localhost/basic/index.php?x=y&z=a&b=c', '/basic', '', 'z', 'a'],
            ['https://localhost/basic/?x=y&z=a&b=c', '/basic', '', 'b', 'c'],
            ['https://localhost/basic/bla/bla?x=y&z=a&b=c', '/basic', '/bla/bla', 'z', 'a'],
        ];
    }
}