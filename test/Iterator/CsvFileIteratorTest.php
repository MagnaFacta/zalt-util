<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Iterator
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Iterator;

use PHPUnit\Framework\TestCase;

/**
 * @package    Zalt
 * @subpackage Iterator
 * @since      Class available since version 1.0
 */
class CsvFileIteratorTest extends TestCase
{
    /**
     *
     * @param string $filename
     * @return CsvFileIterator
     */
    protected function getIterator($filename)
    {
        return new CsvFileIterator($filename);
    }

    public function testCount()
    {
        $filename = str_replace('.php', '.csv', __FILE__);
        $iterator = $this->getIterator($filename);

        $count = $iterator->count();
        $this->assertEquals(3, $count);
    }

    public function testReadAllLines()
    {
        $filename = str_replace('.php', '.csv', __FILE__);
        $iterator = $this->getIterator($filename);
        $actual   = [];
        foreach ($iterator as $line) {
            $actual[] = $line;
        }

        $expected = [
            [
                'line'  => 1,
                'to'    => 'a,',
                'split' => 'b'
            ],
            [
                'line'  => 2,
                'to'    => 'c"',
                'split' => 'd'
            ],
            [
                'line'  => 3,
                'to'    => 'e"',
                'split' => 'f'
            ]
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testSerialize()
    {
        $filename = __CLASS__ . '.csv';
        $iterator = $this->getIterator($filename);
        $iterator->next();  //We are at the second record now
        $expected = $iterator->current();

        $frozen = serialize($iterator);
        $newIterator = unserialize($frozen);

        $actual = $newIterator->current();
        $this->assertEquals($expected, $actual);
    }

}