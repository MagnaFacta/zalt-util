<?php

declare(strict_types=1);

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Base;

use Mezzio\Helper\UrlHelper;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Zalt\Mock\SimpleFlashRequestFactory;
use Zalt\Mock\SimpleRequestHandler;
use Zalt\Mock\SimpleRouter;
use Zalt\Mock\SimpleServiceManager;

/**
 *
 * @package    Zalt
 * @subpackage Base
 * @since      Class available since version 1.0
 */
class BaseUrlMiddlewareTest extends TestCase
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
        $buf     = new BaseUrlFinder();
        $request = SimpleFlashRequestFactory::createWithoutServiceManager($inputUrl);
        
        $bum = new BaseUrlMiddleware($buf, new UrlHelper(new SimpleRouter()));
        $bum->process($request, SimpleRequestHandler::getInstance());
        $baseurl = $bum->getBaseUrl();
        
        $this->assertEquals($expected, $baseurl->getBaseUrl());
    }
}