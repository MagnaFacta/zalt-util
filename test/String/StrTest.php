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
     * Test that alphaNum removes non-alphanumeric characters without allowing spaces.
     */
    public function testAlphaNumRemovesNonAlphaNumericCharacters()
    {
        $input = 'Hello, World! 123.';
        $expected = 'HelloWorld123';
        $this->assertEquals($expected, Str::alphaNum($input));
    }


    /**
     * Test that alphaNum retains alphanumeric characters and spaces when $allowSpaces is true.
     */
    public function testAlphaNumAllowsSpacesWhenFlagIsTrue()
    {
        $input = 'Hello, World! 123.';
        $expected = 'Hello World 123';
        $this->assertEquals($expected, Str::alphaNum($input, true));
    }

    /**
     * Test that alphaNum removes spaces when $allowSpaces is false.
     */
    public function testAlphaNumRemovesSpacesWhenFlagIsFalse()
    {
        $input = 'Hello World 123';
        $expected = 'HelloWorld123';
        $this->assertEquals($expected, Str::alphaNum($input, false));
    }

    /**
     * Test that alphaNum processes an empty string correctly.
     */
    public function testAlphaNumHandlesEmptyString()
    {
        $input = '';
        $expected = '';
        $this->assertEquals($expected, Str::alphaNum($input));
    }

    /**
     * Test that alphaNum retains only alphabetic characters when no digits or symbols are present.
     */
    public function testAlphaNumHandlesAlphabeticInput()
    {
        $input = 'JustSimpleText';
        $expected = 'JustSimpleText';
        $this->assertEquals($expected, Str::alphaNum($input));
    }

    /**
     * Test that alphaNum retains only numeric characters when no letters or symbols are present.
     */
    public function testAlphaNumHandlesNumericInput()
    {
        $input = '1234567890';
        $expected = '1234567890';
        $this->assertEquals($expected, Str::alphaNum($input));
    }

    /**
     * Test that alphaNum removes all characters from a string with no alphanumeric content.
     */
    public function testAlphaNumRemovesAllNonAlphaNumericContent()
    {
        $input = '!@#$%^&*()_+-=[]{}|;:",.<>?/`~';
        $expected = '';
        $this->assertEquals($expected, Str::alphaNum($input));
    }

    /**
     * Test that camel converts strings with spaces to camel case.
     */
    public function testCamelConvertsSpacesToCamelCase()
    {
        $input = 'hello world example';
        $expected = 'helloWorldExample';
        $this->assertEquals($expected, Str::camel($input));
    }

    /**
     * Test that camel converts strings with dashes to camel case.
     */
    public function testCamelConvertsDashesToCamelCase()
    {
        $input = 'hello-world-example';
        $expected = 'helloWorldExample';
        $this->assertEquals($expected, Str::camel($input));
    }

    /**
     * Test that camel converts strings with underscores to camel case.
     */
    public function testCamelConvertsUnderscoresToCamelCase()
    {
        $input = 'hello_world_example';
        $expected = 'helloWorldExample';
        $this->assertEquals($expected, Str::camel($input));
    }

    /**
     * Test that camel does not change already camel-cased strings.
     */
    public function testCamelUnchangedForCamelCaseInput()
    {
        $input = 'helloWorldExample';
        $expected = 'helloWorldExample';
        $this->assertEquals($expected, Str::camel($input));
    }

    /**
     * Test that camel handles empty string input correctly.
     */
    public function testCamelHandlesEmptyString()
    {
        $input = '';
        $expected = '';
        $this->assertEquals($expected, Str::camel($input));
    }
}