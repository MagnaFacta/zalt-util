<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Base
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Base;

use IPLib\Factory;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package    Zalt
 * @subpackage Base
 * @since      Class available since version 1.0
 */
class RequestUtil
{
    private static array $trustedProxies = [];

    private static string $trustedProxyIpHeader = 'HTTP_X_FORWARDED_FOR';

    public static function getClientIp(ServerRequestInterface $request): ?string
    {
        $serverParams = $request->getServerParams();
        $ip = $serverParams['REMOTE_ADDR'] ?? null;

        if (!self::isFromTrustedProxy($ip)) {
            return $ip;
        }

        if (isset($serverParams[self::$trustedProxyIpHeader])) {
            $proxiedIps = explode(',', $serverParams[self::$trustedProxyIpHeader]);
            return trim($proxiedIps[0]);
        }

        return $ip;
    }

    public static function getCurrentSite(ServerRequestInterface $request): ?string
    {
        $hosts = [];
        $serverParams = $request->getServerParams();

        if (isset($serverParams['HTTP_HOST'])) {
            $hosts[] = $serverParams['HTTP_HOST'];
        }
        if (isset($serverParams['SERVER_NAME'])) {
            $hosts[] = $serverParams['SERVER_NAME'];
        }

        foreach(array_unique($hosts) as $host) {
            $url = self::getUrlFromHost($request, $host);
            if ($url !== null) {
                return $url;
            }
        }

        return null;
    }

    public static function getCurrentUrl(ServerRequestInterface $request): ?string
    {
        $serverParams = $request->getServerParams();

        $site = BaseDir::removeBaseDir(self::getCurrentSite($request));
        $url = $site . ($serverParams['REQUEST_URI'] ?? '');
        $params = $serverParams['QUERY_STRING'] ?? '';
        if ($params) {
            return $url . '?' . $params;
        }
        return $url;
    }

    public static function getProtocol(ServerRequestInterface $request): string
    {
        if (self::isSecure($request)) {
            return 'https';
        }

        return 'http';
    }

    public static function isFromTrustedProxy(string $ip): bool
    {
        if (!self::$trustedProxies) {
            return false;
        }
        $address = Factory::parseAddressString($ip);
        foreach(self::$trustedProxies as $trustedProxy) {
            $range = Factory::parseRangeString($trustedProxy);
            if ($address->matches($range)) {
                return true;
            }
        }

        return false;
    }

    public static function isSecure(ServerRequestInterface $request): bool
    {
        $serverParams = $request->getServerParams();
        if (isset($serverParams['HTTP_X_FORWARDED_SCHEME'])) {
            return strtolower($serverParams['HTTP_X_FORWARDED_SCHEME']) === 'https';
        }
        if (isset($serverParams['REQUEST_SCHEME'])) {
            return strtolower($serverParams['REQUEST_SCHEME']) === 'https';
        }
        if (isset($serverParams['HTTPS'])) {
            return $serverParams['HTTPS'] == '1';
        }
        return false;
    }

    /**
     * @param array $trustedProxies
     */
    public static function setTrustedProxies(array $trustedProxies): void
    {
        self::$trustedProxies = $trustedProxies;
    }

    public static function setTrustedProxyIpHeader(string $trustedProxyIpHeader): void
    {
        self::$trustedProxyIpHeader = $trustedProxyIpHeader;
    }

    protected static function getUrlFromHost(ServerRequestInterface $request, string $host): string
    {
        if (str_contains($host, '://')) {
            return BaseDir::addBaseDir($host);
        }
        $protocol = self::getProtocol($request);
        return $protocol . '://' . BaseDir::addBaseDir($host);
    }
}