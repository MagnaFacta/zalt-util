<?php

/**
 *
 * @package    Zalt
 * @subpackage Lists
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Lists;

use PHPUnit\Framework\TestCase;

/**
 *
 * @package    Zalt
 * @subpackage Lists
 * @since      Class available since version 1.0
 */
class LookupListTest extends TestCase
{
    public function testChangingList()
    {
        $items = [
            'abc' => 'a string',
            'def' => '',
            'obj' => new \stdClass(),
        ];

        $list = new LookupList();

        foreach ($items as $key => $value) {
            $this->assertEquals($value, $list->get($key, $value));
            $this->assertEmpty($list->get($key));
            $list->add($key, $value);
            $this->assertEquals($value, $list->get($key));
        }
    }
    
    public function testEmptyList()
    {
        $list = new LookupList([]);
        
        $this->assertEmpty($list->get('abc'));
        $this->assertEmpty($list->get('def'));
        $this->assertEmpty($list->get());
        $this->assertIsArray($list->get());
    }
    
    public function testIntializedList()
    {
        $items = [
            'abc' => 'a string',
            'def' => '',
            'obj' => new \stdClass(),
        ];
        
        $list = new LookupList($items);
        
        foreach ($items as $key => $value) {
            $this->assertEquals($value, $list->get($key));
        }
        foreach (['_1', '_2'] as $key) {
            $this->assertEmpty($list->get($key));
        }
    }
}