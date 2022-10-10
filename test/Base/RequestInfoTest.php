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
    public function testUrls($url, $base, $param = null, $value = null)
    {
        $request = SimpleFlashRequestFactory::createWithoutServiceManager($url);
        
        $requestInfo = RequestInfoFactory::getMezzioRequestInfo($request);
        
        $this->assertEquals($requestInfo->getBasePath(), $base);
        if ($param) {
            $this->assertEquals($requestInfo->getParam($param), $value);
        }
    }
    
    public function urlProvider()
    {
        return [
            ['https://zelt.test.nl/', '/'],
            ['https://zelt.test.nl/index.php?x=y', '/', 'x', 'y'],
            ['https://localhost/base', '/base'],
            ['https://localhost/base/index.php', '/base'],
            ['https://localhost/base/?x=y', '/base', 'x', 'y'],
            ['https://localhost/basic/?x=y&z=a&b=c', '/basic', 'x', 'y'],
            ['https://localhost/basic/index.php?x=y&z=a&b=c', '/basic', 'z', 'a'],
            ['https://localhost/basic/?x=y&z=a&b=c', '/basic', 'b', 'c'],
        ];
    }
}