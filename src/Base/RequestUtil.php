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
        foreach(static::$trustedProxies as $trustedProxy) {
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
}