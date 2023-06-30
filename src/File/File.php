<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage File
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\File;

use Zalt\Base\Error;
use Zalt\Ra\Ra;

/**
 * @package    Zalt
 * @subpackage File
 * @since      Class available since version 1.0
 */
class File
{
    /**
     * @var string[] Extensions for office and adobe documents
     */
    static public array $documentExtensions = ['csv', 'doc', 'docx', 'md', 'ods', 'odt', 'pdf', 'ppt', 'pptx', 'ps', 'rtf', 'xml', 'xls', 'xslx'];

    /**
     * @var string[] Extensions for image files
     */
    static public array $imageExtensions = ['bmp', 'eps', 'gif', 'ico', 'img', 'jpg', 'jpeg', 'png', 'svg', 'swf', 'tif', 'tiff'];

    /**
     * @var string[] Extensions for text documents
     */
    static public array $textExtensions = ['csv', 'ini', 'log', 'txt', 'xml'];

    /**
     * @var string[] Extensions for videos
     */
    static public array $videoExtensions = ['avi', 'mkv', 'mov', 'mp4', 'mpeg', 'mpg', 'ogg', 'qt', 'swf', 'webm', 'wmv'];

    /**
     * Make sure the filename is actually allowd by underlying file systems
     *
     * @param string $filename
     * @return string
     */
    public static function cleanupName(array $filename): string
    {
        return preg_replace('/[\|\\\?\*<\":>\+\[\]\/]\x00/', "_", $filename);
    }

    /**
     * Make sure the slashes point the way of the underlying server only
     *
     * @param string $filename
     * @return string
     */
    public static function cleanupSlashes(string $filename): string
    {
        return str_replace('\\' === DIRECTORY_SEPARATOR ? '/' : '\\', DIRECTORY_SEPARATOR, $filename);
    }

    /**
     * @param array $extensions An [optionally nested] array of file extensions
     * @param string $startName A start path / regular expression
     * @param boolean $caseSensitive
     * @return string A preg expression for the extensions
     */
    public static function createMask(array $extensions, string $startName = '', bool $caseSensitive = false): string
    {
        $masks = Ra::flatten((array) $extensions);

        if ($startName) {
            return '/' . $startName . '.*\\.(' . implode('|', $masks) . ')$/';
        }

        return '/.+\\.(' . implode('|', $masks) . ')$/' . ($caseSensitive ? '' : 'i');
    }

    /**
     * Creates a temporary empty file in the directory but first cleans
     * up any files older than $keepFor in that directory.
     *
     * When no directory is used sys_get_temp_dir() is used and no cleanup is performed.
     *
     * @param ?string $dir The directory for the files, system temp if null
     * @param string $prefix Optional prefix
     * @param int $keepFor The number of second a file is kept, use 0 for always
     * @return string The name of the temporary file
     */
    public static function createTemporaryIn(?string $dir = null, string $prefix = '', int $keepFor = 86400): string
    {
        $filename = self::getTemporaryIn($dir, $prefix, $keepFor);

        file_put_contents($filename, '');

        return $filename;
    }

    /**
     * Ensure the directory does really exist or throw an exception othewise
     *
     * @param string $dir The path of the directory
     * @param int $mode Unix file mask mode, ignored on Windows
     * @return string the directory
     * @throws \Zalt\File\FileException
     */
    public static function ensureDir(string $dir, int $mode = 0777): string
    {
        // Clean up directory name
        $dir = self::cleanupSlashes($dir);

        // Cascade to ensure parent dir creation (not all operating systems allow cascaded creation)
        $parent = dirname($dir);
        if (strlen($parent) > 1 && (! is_dir($parent))) {
            self::ensureDir($parent, $mode);
        }
        if (! is_dir($dir)) {
            if (! @mkdir($dir, $mode, true)) {
                throw new FileException(sprintf(
                    "Could not create '%s' directory: %s.",
                    $dir,
                    Error::getLastPhpErrorMessage('reason unknown')
                ));
            }
        }

        return $dir;
    }

    /**
     * Format in drive units
     *
     * @param int $size
     * @return string
     * /
    public static function getByteSized($size)
    {
        $units = array( '', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return \Zend_Locale_Format::toInteger($size / pow(1024, $power)) . ' ' . $units[$power];
    }

    /**
     * Returns an array containing all the files (not directories) in a
     * recursive directory listing from $dir.
     *
     * @param string $dir
     * @return array
     */
    public static function getFilesRecursive(string $dir): array
    {
        $results = array();

        if (is_dir($dir)) {
            foreach (scandir($dir) as $file) {
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($path)) {
                    if (('.' !== $file) && ('..' !== $file)) {
                        $results = array_merge($results, self::getFilesRecursive($path));
                    }
                } else {
                    $results[] = $path;
                }
            }
        }

        return $results;
    }

    /**
     * Creates a temporary filename in the directory but first cleans
     * up any files older than $keepFor in that directory.
     *
     * When no directory is used sys_get_temp_dir() is used and no cleanup is performed.
     *
     * @param ?string $dir The directory for the files, system temp if null
     * @param string $prefix Optional prefix
     * @param int $keepFor The number of second a file is kept, use 0 for always
     * @return string The name of the temporary file
     */
    public static function getTemporaryIn(?string $dir = null, string $prefix = '', int $keepFor = 86400): string
    {
        if (null === $dir) {
            $output = tempnam(sys_get_temp_dir(), $prefix);
        } else {
            self::ensureDir($dir);

            if ($keepFor) {
                // Clean up old temporaries
                foreach (glob($dir . '/*', GLOB_NOSORT) as $filename) {
                    if ((!is_dir($filename)) && (filemtime($filename) + $keepFor < time())) {
                        @unlink($filename);
                    }
                }
            }

            $output = tempnam($dir, $prefix);
        }
        chmod($output, 0777);

        return $output;
    }

    /**
     * Is the path a rooted path?
     * On Windows this does not require a drive letter, but one is allowed
     *
     * Check OS specific plus check for urls
     *
     * @param string $path
     * @return boolean
     */
    public static function isRootPath(string $path): bool
    {
        if (! $path) {
            return false;
        }
        // Quick checkes first and then something just in case
        if (('\\' == $path[0]) || ('/' == $path[0]) || \MUtil\StringUtil\StringUtil::startsWith($path, DIRECTORY_SEPARATOR)) {
            return true;
        }
        // One more check for windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return preg_match('#^[a-zA-Z]:\\\\#', $path);
        }
        // The check for uri's (frp, http, https
        return preg_match('#^[a-zA-Z]+://#', $path);
    }

    /**
     * Returns true when running on Windows machine
     *
     * @return boolean
     * /
    public static function isOnWindows(): bool
    {
        return (DIRECTORY_SEPARATOR == '\\');
    }

    /**
     * Removes the c: part from the filename
     *
     * @param string $path
     * @return string
     */
    public static function removeWindowsDriveLetter(string $path): string
    {
        return preg_replace('/^([a-zA-Z]:)/', '', $path);
    }
}