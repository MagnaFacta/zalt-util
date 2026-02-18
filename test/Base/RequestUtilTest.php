<?php

declare(strict_types=1);

namespace Zalt\Base;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class RequestUtilTest extends TestCase
{
    public static function getClientIpDataProvider(): array
    {
        return [
            'without trusted proxy' => [
                'serverParams' => ['REMOTE_ADDR' => '192.168.0.1'],
                'trustedProxies' => [],
                'trustedProxyIpHeader' => 'HTTP_X_FORWARDED_FOR',
                'expectedIp' => '192.168.0.1',
            ],
            'with trusted proxy and forwarded header' => [
                'serverParams' => [
                    'REMOTE_ADDR' => '192.168.0.1',
                    'HTTP_X_FORWARDED_FOR' => '203.0.113.1, 203.0.113.2',
                ],
                'trustedProxies' => ['192.168.0.0/24'],
                'trustedProxyIpHeader' => 'HTTP_X_FORWARDED_FOR',
                'expectedIp' => '203.0.113.1',
            ],
            'with untrusted proxy' => [
                'serverParams' => [
                    'REMOTE_ADDR' => '192.168.0.1',
                    'HTTP_X_FORWARDED_FOR' => '203.0.113.1',
                ],
                'trustedProxies' => ['10.0.0.0/8'],
                'trustedProxyIpHeader' => 'HTTP_X_FORWARDED_FOR',
                'expectedIp' => '192.168.0.1',
            ],
            'with custom proxy IP header' => [
                'serverParams' => [
                    'REMOTE_ADDR' => '192.168.0.1',
                    'HTTP_CUSTOM_FORWARDED_FOR' => '198.51.100.1, 198.51.100.2',
                ],
                'trustedProxies' => ['192.168.0.0/24'],
                'trustedProxyIpHeader' => 'HTTP_CUSTOM_FORWARDED_FOR',
                'expectedIp' => '198.51.100.1',
            ],
            'no remote address' => [
                'serverParams' => [],
                'trustedProxies' => [],
                'trustedProxyIpHeader' => 'HTTP_X_FORWARDED_FOR',
                'expectedIp' => null,
            ],
        ];
    }

    /**
     * @dataProvider getClientIpDataProvider
     */
    public function testGetClientIp(array $serverParams, array $trustedProxies, string $trustedProxyIpHeader, ?string $expectedIp): void
    {
        RequestUtil::setTrustedProxies($trustedProxies);
        RequestUtil::setTrustedProxyIpHeader($trustedProxyIpHeader);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getServerParams')->willReturn($serverParams);

        $this->assertSame($expectedIp, RequestUtil::getClientIp($request));
    }


    public static function getCurrentSiteDataProvider(): array
    {
        return [
            'with HTTP_HOST only' => [
                'baseDir' => '/',
                'serverParams' => ['HTTP_HOST' => 'example.com'],
                'expectedUrl' => 'http://example.com',
            ],
            'with baseDir and HTTP_HOST only' => [
                'baseDir' => '/baseDir',
                'serverParams' => ['HTTP_HOST' => 'example.com'],
                'expectedUrl' => 'http://example.com/baseDir',
            ],
            'with SERVER_NAME only' => [
                'baseDir' => '/',
                'serverParams' => ['SERVER_NAME' => 'example.org'],
                'expectedUrl' => 'http://example.org',
            ],
            'with baseDir and SERVER_NAME only' => [
                'baseDir' => '/baseDir',
                'serverParams' => ['SERVER_NAME' => 'example.org'],
                'expectedUrl' => 'http://example.org/baseDir',
            ],
            'with HTTP_HOST and SERVER_NAME different' => [
                'baseDir' => '/',
                'serverParams' => ['HTTP_HOST' => 'host.com', 'SERVER_NAME' => 'server.org'],
                'expectedUrl' => 'http://host.com',
            ],
            'with HTTPS protocol' => [
                'baseDir' => '/',
                'serverParams' => ['HTTP_HOST' => 'secure.com', 'HTTPS' => '1'],
                'expectedUrl' => 'https://secure.com',
            ],
            'no host information' => [
                'baseDir' => '/',
                'serverParams' => [],
                'expectedUrl' => null,
            ],
        ];
    }

    /**
     * @dataProvider getCurrentSiteDataProvider
     */
    public function testGetCurrentSite(string $baseDir, array $serverParams, ?string $expectedUrl): void
    {
        BaseDir::setBaseDir($baseDir);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getServerParams')->willReturn($serverParams);

        $this->assertSame($expectedUrl, RequestUtil::getCurrentSite($request));
    }

    public static function isSecureDataProvider(): array
    {
        return [
            'default http protocol' => [
                'serverParams' => ['HTTPS' => null],
                'expected' => false,
            ],
            'explicit https in proxy scheme header' => [
                'serverParams' => ['HTTP_X_FORWARDED_PROTO' => 'https'],
                'expected' => true,
            ],
            'explicit http in proxy scheme header' => [
                'serverParams' => ['HTTP_X_FORWARDED_PROTO' => 'http'],
                'expected' => false,
            ],
            'regular https server param' => [
                'serverParams' => ['REQUEST_SCHEME' => 'https'],
                'expected' => true,
            ],
            'HTTPS server param set to on' => [
                'serverParams' => ['HTTPS' => 'on'],
                'expected' => false,
            ],
            'HTTPS server param set to 1' => [
                'serverParams' => ['HTTPS' => '1'],
                'expected' => true,
            ],
            'default http with no indicators' => [
                'serverParams' => [],
                'expected' => false,
            ],
        ];
    }

    /**
     * @dataProvider isSecureDataProvider
     */
    public function testIsSecure(array $serverParams, bool $expected): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getServerParams')->willReturn($serverParams);

        $this->assertSame($expected, RequestUtil::isSecure($request));
    }
}