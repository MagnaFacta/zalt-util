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

    public static function cleanupSlashesDataProvider(): array
    {
        return [
            'unix-style'       => ['path/to/file', 'path' .  DIRECTORY_SEPARATOR . 'to' .  DIRECTORY_SEPARATOR . 'file'],
            'windows-relative' => ['path\\to\\file', 'path' .  DIRECTORY_SEPARATOR . 'to' .  DIRECTORY_SEPARATOR . 'file'],
            'mixed-relative'   => ['path\\to/file', 'path' .  DIRECTORY_SEPARATOR . 'to' .  DIRECTORY_SEPARATOR . 'file'], // Mixed (Windows -> Unix)
            'windows-absolute' => ['C:\\path\\to\\file', 'C:' .  DIRECTORY_SEPARATOR . 'path' .  DIRECTORY_SEPARATOR . 'to' .  DIRECTORY_SEPARATOR . 'file'],
            'mixed-absolute'   => ['/path\\to\\file', DIRECTORY_SEPARATOR . 'path' .  DIRECTORY_SEPARATOR . 'to' .  DIRECTORY_SEPARATOR . 'file'], // Mixed starting with Unix
            'drive-absolute'   => ['C:/path/to/file', 'C:' .  DIRECTORY_SEPARATOR . 'path' .  DIRECTORY_SEPARATOR . 'to' .  DIRECTORY_SEPARATOR . 'file'], // Already cleaned
        ];
    }

    /**
     * @dataProvider cleanupSlashesDataProvider
     */
    public function testCleanupSlashes(string $input, string $expected): void
    {
        $this->assertSame($expected, File::cleanupSlashes($input));
    }

    public static function createMaskDataProvider(): array
    {
        return [
            'simple extensions' => [
                ['txt', 'jpg'],
                '',
                false,
                '/.+\\.(txt|jpg)$/i'
            ],
            'case sensitive' => [
                ['TXT', 'JPG'],
                '',
                true,
                '/.+\\.(TXT|JPG)$/'
            ],
            'with startName' => [
                ['csv', 'xml'],
                'data',
                false,
                '/data.*\\.(csv|xml)$/i'
            ],
            'nested extensions' => [
                [['doc', 'docx'], ['xls', 'xlsx']],
                '',
                false,
                '/.+\\.(doc|docx|xls|xlsx)$/i'
            ],
        ];
    }

    /**
     * @dataProvider createMaskDataProvider
     */
    public function testCreateMask(array $extensions, string $startName, bool $caseSensitive, string $expected): void
    {
        $this->assertSame($expected, File::createMask($extensions, $startName, $caseSensitive));
    }

    /**
     * @throws \Zalt\File\FileException
     */
    public function testEnsureDirCreatesDirectory(): void
    {
        $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'testEnsureDir';
        if (is_dir($tempDir)) {
            rmdir($tempDir);
        }

        $this->assertDirectoryDoesNotExist($tempDir);

        $result = File::ensureDir($tempDir);

        $this->assertDirectoryExists($result);

        rmdir($tempDir);
    }

    /**
     * @throws \Zalt\File\FileException
     */
    public function testEnsureDirThrowsExceptionIfCannotCreate(): void
    {
        $this->expectException(FileException::class);

        if (DIRECTORY_SEPARATOR == '\\') {
            $parentDir  = sys_get_temp_dir() . '\\testEnsureDirInvalid';
            $invalidDir = $parentDir . '\\invalid*subdir';

        } else {
            $parentDir  = sys_get_temp_dir() . '/testEnsureDirInvalid';
            $invalidDir = $parentDir . '/invalid/subdir';
            mkdir($parentDir, 0444); // Make parent directory non-writable
        }

        try {
            File::ensureDir($invalidDir);
        } finally {
            if (DIRECTORY_SEPARATOR == '/') {
                chmod($parentDir, 0777);
            }
            rmdir($parentDir);
        }
    }

    /**
     * @throws \Zalt\File\FileException
     */
    public function testEnsureDirHandlesCascadingDirectories(): void
    {
        $parentDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'testEnsureDirParent';
        $nestedDir1 = $parentDir . DIRECTORY_SEPARATOR . 'nested';
        $nestedDir2 = $nestedDir1 . DIRECTORY_SEPARATOR . 'subdir';
        if (is_dir($nestedDir2)) {
            rmdir($nestedDir2);
        }
        if (is_dir($nestedDir1)) {
            rmdir($nestedDir1);
        }
        if (is_dir($parentDir)) {
            rmdir($parentDir);
        }

        $this->assertDirectoryDoesNotExist($nestedDir2);

        $result = File::ensureDir($nestedDir2);

        $this->assertDirectoryExists($result);

        rmdir($nestedDir2);
        rmdir($nestedDir1);
        rmdir($parentDir);
    }

    public function testEnsureDirDoesNothingForExistingDirectory(): void
    {
        $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'testExistingDir';
        mkdir($tempDir);

        $this->assertDirectoryExists($tempDir);

        $result = File::ensureDir($tempDir);

        $this->assertSame($tempDir, $result);

        rmdir($tempDir);
    }

    public static function getByteSizedDataProvider()
    {
        return [
            '100bytes' => [100, '100 '],
            'str1024' => ['1024', '1 Kb'],
            '1Kb' => [1024, '1 Kb'],
            '2Kb' => [2048, '2 Kb'],
            '1Mb' => [1048576, '1 Mb'],
            '1Mb+' => [1048577, '1 Mb'],
            '2Mb' => [2097154, '2 Mb'],
            '0.99Gb' => [1073741823, '1024 Mb'],
            '1Gb' => [1073741824, '1 Gb'],
            '1Tb' => [1099511627776, '1 Tb'],
            '2.1Tb' => [2308974418329, '2 Tb'],
            '2.49Tb' => [2748779069439, '2 Tb'],
            '2.5Tb' => [2748779069440, '3 Tb'],
            '2.6Tb' => [2858730232218, '3 Tb'],
            '26Tb' => [28587302322176, '26 Tb'],
            '1Pb' => [1125899906842624, '1 Pb'],
            '1Eb' => [1.152921504606847e+18, '1 Eb'],
        ];
    }

    /**
     * @dataProvider getByteSizedDataProvider
     */
    public function testGetByteSized(mixed $input, string $output)
    {
        $result = File::getByteSized($input);

        $this->assertEquals($output, $result);
    }
}