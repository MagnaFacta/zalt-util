<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage File
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\File;

use PHPUnit\Framework\TestCase;

/**
 * @package    Zalt
 * @subpackage File
 * @since      Class available since version 1.0
 */
class FileTest extends TestCase
{
    public static function cleanupFilenameDataProvider()
    {
        return [
            ['file.txt', 'file.txt'],
            ['file|txt', 'file_txt'],
            ['file\\txt', 'file_txt'],
            ['file?txt', 'file_txt'],
            ['file"txt', 'file_txt'],
            ['file*txt', 'file_txt'],
            ['file<txt', 'file_txt'],
            ['file:txt', 'file_txt'],
            ['file>txt', 'file_txt'],
            ['file+txt', 'file_txt'],
            ['file[txt', 'file_txt'],
            ['file]txt', 'file_txt'],
            ['file/txt', 'file_txt'],
            ['file' . chr(0) . 'txt', 'file_txt'],
            ['fïlë txt', 'fïlë txt'],
            ];
    }

    /**
     * @dataProvider cleanupFilenameDataProvider
     */

    public function testCleanupFilename(string $input, string $output)
    {
        $this->assertEquals(File::cleanupName($input), $output);
    }
}