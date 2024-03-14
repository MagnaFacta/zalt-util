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
class BaseDir
{
    protected static ?string $baseDir = null;

    public static function addBaseDir(string $hostUrl, string $baseDir = null)
    {
        if (! $baseDir) {
            $baseDir = self::getBaseDir();
        }

        if (str_ends_with(strtolower($hostUrl), strtolower($baseDir))) {
            return $hostUrl;
        }
        return trim($hostUrl, '/') . $baseDir;
    }

    /**
     * This function checks if the found filename has an extension, in that case the filename itself
     * is removed from the basedir.
     *
     * @param array $globals
     * @param bool $testing
     * @return string The subdirectory of the webserver where the applications is, / for root
     */
    public static function findBaseDir(array $globals, bool $testing = false): string
    {
        if (php_sapi_name() === "cli" && (! $testing)) {
            return '/';
        }
        $scriptFile = null;
        foreach (['SCRIPT_NAME', 'PHP_SELF', 'ORIG_SCRIPT_NAME', 'SCRIPT_FILENAME'] as $var) {
            if (isset($globals[$var])) {
                $scriptFile = $globals[$var];
                break;
            }
        }
        if ($scriptFile) {
            if (str_contains(basename($scriptFile), '.')) {
                $scriptFile = dirname($scriptFile);
            }
            if ($scriptFile) {
                return $scriptFile;
            }
        }

        return '/';
    }

    /**
     * Sets the baseDir from $_SERVER if not yet set
     *
     * @return string The current string to add if the application is in a subdirectory of the webserver or an empty string
     */
    public static function getBaseDir(): string
    {
        if (null === self::$baseDir) {
            // file_put_contents('data/logs/echo.txt', __CLASS__ . '->' . __FUNCTION__ . '(' . __LINE__ . '): ' .  print_r($_SERVER, true) . "\n", FILE_APPEND);
            self::setBaseDir(self::findBaseDir($_SERVER));
        }

        return self::$baseDir;
    }

    /**
     * Sets up and cleans the input for using getBaseDir()
     * @param string $baseDir
     * @return void
     */
    public static function setBaseDir(string $baseDir): void
    {
        if (empty($baseDir)) {
            self::$baseDir = '';
            return;
        }
        $baseDir = str_replace('\\', '/', $baseDir);
        if (! str_starts_with($baseDir, '/')) {
            $baseDir = '/' . $baseDir;
        }
        self::$baseDir = rtrim($baseDir, '/');
    }
    
    public static function withBaseDir(ServerRequestInterface $request): ServerRequestInterface
    {
        if (! self::$baseDir) {
            self::setBaseDir(self::findBaseDir($request->getServerParams()));
        }

        $baseDir = rtrim(self::getBaseDir(), '/');
        if (! $baseDir) {
            return $request;
        }
        
        $uri  = $request->getUri();
        $host = $uri->getHost();
        if (str_ends_with($host, $baseDir)) {
            return $request;
        }
        $uriPath = $uri->withHost($uri->getHost() . $baseDir);
        if (str_starts_with($uri->getPath(), $baseDir)) {
            $uriPath = $uriPath->withPath(substr($uri->getPath(), strlen($baseDir)));
        }
        return $request->withUri($uriPath, true);
    }
}