<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Base
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Base;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @package    Zalt
 * @subpackage Base
 * @since      Class available since version 1.0
 */
class RequestUtil
{
    public static function getClientIp(ServerRequestInterface $request = null): ?string
    {
        if ($request) {
            $params = $request->getServerParams();
        } else {
            $params = $_SERVER;
        }

        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $field) {
            if (isset($params[$field])) {
                return $params[$field];
            }
        }
        return null;
    }

    public static function getProtocol(ServerRequestInterface $request): string
    {
        if (self::isSecure($request)) {
            return 'https';
        }

        return 'http';
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
}