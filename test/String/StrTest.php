<?php

namespace Zalt\String;

use PHPUnit\Framework\TestCase;

/**
 * Unit test class for the Str::alphaNum method.
 *
 * The alphaNum method filters a string, removing all non-alphanumeric characters.
 * Optionally, it allows spaces if the $allowSpaces parameter is true.
 */
class StrTest extends TestCase
{
    /**
     * Provides test cases for the alphaNum method.
     *
     * @return array<int, array{string, string, bool}>
     */
    public static function alphaNumDataProvider(): array
    {
        return [
            ['abc123!@#DEF', 'abc123DEF', false],
            ['abc 123!@# DEF', 'abc 123 DEF', true],
            ['!@#', '', false],
            [' 123 ', ' 123 ', true],
            ['', '', false],
        ];
    }

    /**
     * Test alphaNum using dataProvider.
     *
     * @dataProvider alphaNumDataProvider
     */
    public function testAlphaNumWithDataProvider(string $input, string $expected, bool $allowSpaces): void
    {
        $this->assertEquals($expected, Str::alphaNum($input, $allowSpaces));
    }

    /**
     * Provides test cases for the camel method.
     *
     * @return array<int, array{string, string}>
     */
    public static function camelDataProvider(): array
    {
        return [
            ['hello_world', 'helloWorld'],
            ['hello_world', 'helloWorld'], // Check cache
            ['Hello_world', 'helloWorld'],
            ['hello World', 'helloWorld'],
            ['hello-world', 'helloWorld'],
            ['HELLO_WORLD', 'hELLOWORLD'],
            ['  hello   world  ', 'helloWorld'],
            ['', ''],
        ];
    }

    /**
     * Test camel using dataProvider.
     *
     * @dataProvider camelDataProvider
     */
    public function testCamelWithDataProvider(string $input, string $expected): void
    {
        $this->assertEquals($expected, Str::camel($input));
    }

    /**
     * Provides test cases for the kebab method.
     *
     * @return array<int, array{string, string}>
     */
    public static function kebabDataProvider(): array
    {
        return [
            ['hello_world', 'hello-world'],
            ['Hello_world', 'hello-world'],
            ['Hello_world', 'hello-world'], // Check cache
            ['hello World', 'hello-world'],
            ['hello-world', 'hello-world'],
            ['HELLO_WORLD', 'h-e-l-l-o-w-o-r-l-d'],
            ['  hello   world  ', 'hello-world'],
            ['', ''],
        ];
    }

    /**
     * Test kebab using dataProvider.
     *
     * @dataProvider kebabDataProvider
     */
    public function testKebabWithDataProvider(string $input, string $expected): void
    {
        $this->assertEquals($expected, Str::kebab($input));
    }

    /**
     * Provides test cases for the lower method.
     *
     * @return array<int, array{string|null, string}>
     */
    public static function lowerDataProvider(): array
    {
        return [
            ['HELLO', 'hello'],
            ['Hello WORLD', 'hello world'],
            ['123ABC!@#', '123abc!@#'],
            ['HeLlO_wOrLd', 'hello_world'],
            ['HëLlO_wOrLd', 'hëllo_world'],
            ['HËLlO_wOrLd', 'hëllo_world'],
            [null, ''],
            ['', ''],
        ];
    }

    /**
     * Test lower using dataProvider.
     *
     * @dataProvider lowerDataProvider
     */
    public function testLowerWithDataProvider(?string $input, string $expected): void
    {
        $this->assertEquals($expected, Str::lower($input));
    }

    /**
     * Provides test cases for the pascal method.
     *
     * @return array<int, array{string, string}>
     */
    public static function pascalDataProvider(): array
    {
        return [
            ['hello_world', 'HelloWorld'],
            ['Hello_world', 'HelloWorld'],
            ['hello World', 'HelloWorld'],
            ['hello-world', 'HelloWorld'],
            ['  hello   world  ', 'HelloWorld'],
            ['HELLO_WORLD', 'HELLOWORLD'],
            ['', ''],
        ];
    }

    /**
     * Test pascal using dataProvider.
     *
     * @dataProvider pascalDataProvider
     */
    public function testPascalWithDataProvider(string $input, string $expected): void
    {
        $this->assertEquals($expected, Str::pascal($input));
    }

    /**
     * Provides test cases for the slug method.
     *
     * @return array<int, array{string, string, string, array<string, string>}>
     */
    public static function slugDataProvider(): array
    {
        return [
            ['hëllo world!', 'hello-world', '-', []],
            ['hello_world', 'hello-world', '-', []],
            ['Hello@world', 'hello-at-world', '-', ['@' => 'at']],
            ['  hello   world  ', 'hello-world', '-', []],
            ['spËcial_chars', 'special-chars', '-', []],
            ['example/test_case', 'exampletest-case', '-', []],
            ['example/test_case', 'exampletest_case', '_', []],
            ['Multiple@Words@Here', 'multiple-and-words-and-here', '-', ['@' => 'and']],
            ['Custom@Dictionary', 'custom-separator-dictionary', '-', ['@' => 'separator']],
            [ "á|â|à|å|ä ð|é|ê|è|ë í|î|ì|ï ó|ô|ò|ø|õ|ö ú|û|ù|ü æ ç ß abc ABC 123",  "aaaaa-deeee-iiii-oooooo-uuuu-ae-c-ss-abc-abc-123", '-', []]
        ];
    }

    /**
     * Test slug using dataProvider.
     *
     * @dataProvider slugDataProvider
     */
    public function testSlugWithDataProvider(string $input, string $expected, string $separator, array $dictionary): void
    {
        $this->assertEquals($expected, Str::slug($input, $separator, 'en', $dictionary));
    }
}