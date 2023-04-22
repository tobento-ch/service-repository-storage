<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Repository\Storage\Test\Column;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Repository\Storage\Column\Type;

/**
 * TypeTest
 */
class TypeTest extends TestCase
{
    public function testIsPrimaryMethod()
    {
        $this->assertFalse((new Type())->isPrimary());
        $this->assertFalse((new Type(type: 'text'))->isPrimary());
        $this->assertTrue((new Type(type: 'primary'))->isPrimary());
        $this->assertTrue((new Type(type: 'bigPrimary'))->isPrimary());
    }
    
    public function testTypeMethod()
    {
        $this->assertSame('string', (new Type())->type());
        $this->assertSame('text', (new Type(type: 'text'))->type());
        $this->assertSame('primary', (new Type(type: 'primary'))->type());
    }
    
    public function testParametersMethod()
    {
        $this->assertSame([], (new Type())->parameters());
        
        $this->assertSame(
            ['type' => 'text', 'default' => 'foo'],
            (new Type(type: 'text', default: 'foo'))->parameters()
        );
    }
    
    public function testHasMethod()
    {
        $this->assertFalse((new Type())->has('type'));
        $this->assertTrue((new Type(type: 'text'))->has('type'));
        $this->assertTrue((new Type(default: ''))->has('default'));
        $this->assertTrue((new Type(default: null))->has('default'));
        $this->assertTrue((new Type(default: 0))->has('default'));
    }
    
    public function testGetMethod()
    {
        $this->assertSame(null, (new Type())->get('type'));
        $this->assertSame('', (new Type())->get(name: 'type', default: ''));
        $this->assertSame('text', (new Type(type: 'text'))->get(name: 'type', default: ''));
    }
    
    public function testCastMethodWithInt()
    {
        $this->assertSame(1, (new Type(type: 'int'))->cast(value: 1));
        $this->assertSame(0, (new Type(type: 'int'))->cast(value: ''));
        $this->assertSame(0, (new Type(type: 'int'))->cast(value: []));
        $this->assertSame(1, (new Type(type: 'int'))->cast(value: [], default: 1));
    }
    
    public function testCastMethodWithFloat()
    {
        $this->assertSame(1.0, (new Type(type: 'float'))->cast(value: 1));
        $this->assertSame(0., (new Type(type: 'float'))->cast(value: ''));
        $this->assertSame(0., (new Type(type: 'float'))->cast(value: []));
        $this->assertSame(1.0, (new Type(type: 'float'))->cast(value: [], default: 1));
    }
    
    public function testCastMethodWithString()
    {
        $this->assertSame('foo', (new Type(type: 'string'))->cast(value: 'foo'));
        $this->assertSame('1', (new Type(type: 'string'))->cast(value: 1));
        $this->assertSame('', (new Type(type: 'string'))->cast(value: []));
        $this->assertSame('foo', (new Type(type: 'string'))->cast(value: [], default: 'foo'));
    }
    
    public function testCastMethodWithBool()
    {
        $this->assertSame(true, (new Type(type: 'bool'))->cast(value: true));
        $this->assertSame(true, (new Type(type: 'bool'))->cast(value: 1));
        $this->assertSame(false, (new Type(type: 'bool'))->cast(value: []));
        $this->assertSame(true, (new Type(type: 'bool'))->cast(value: 'string'));
        $this->assertSame(true, (new Type(type: 'bool'))->cast(value: [], default: true));
    }
    
    public function testCastMethodWithArray()
    {
        $this->assertSame(['foo'], (new Type(type: 'array'))->cast(value: ['foo']));
        $this->assertSame([], (new Type(type: 'array'))->cast(value: 1));
        $this->assertSame([], (new Type(type: 'array'))->cast(value: 'foo'));
        $this->assertSame(['foo'], (new Type(type: 'array'))->cast(value: '1', default: ['foo']));
    }
    
    public function testCastMethodWithDatetime()
    {
        $this->assertSame('2023-11-25 00:00:00', (new Type(type: 'datetime'))->cast(value: '2023-11-25 00:00:00'));
        $this->assertSame('1', (new Type(type: 'datetime'))->cast(value: 1));
        $this->assertSame('', (new Type(type: 'datetime'))->cast(value: []));
        $this->assertSame('2023-11-25 00:00:00', (new Type(type: 'datetime'))->cast(value: [], default: '2023-11-25 00:00:00'));
    }
    
    public function testCastMethodWithDate()
    {
        $this->assertSame('2023-11-25', (new Type(type: 'date'))->cast(value: '2023-11-25'));
        $this->assertSame('1', (new Type(type: 'date'))->cast(value: 1));
        $this->assertSame('', (new Type(type: 'date'))->cast(value: []));
        $this->assertSame('2023-11-25', (new Type(type: 'date'))->cast(value: [], default: '2023-11-25'));
    }
    
    public function testCastMethodWithTime()
    {
        $this->assertSame('10:09:08', (new Type(type: 'time'))->cast(value: '10:09:08'));
        $this->assertSame('1', (new Type(type: 'time'))->cast(value: 1));
        $this->assertSame('', (new Type(type: 'time'))->cast(value: []));
        $this->assertSame('10:09:08', (new Type(type: 'time'))->cast(value: [], default: '10:09:08'));
    }
    
    public function testCastMethodWithTimestamp()
    {
        $this->assertSame('1272509157', (new Type(type: 'timestamp'))->cast(value: '1272509157'));
        $this->assertSame('1', (new Type(type: 'timestamp'))->cast(value: 1));
        $this->assertSame('', (new Type(type: 'timestamp'))->cast(value: []));
        $this->assertSame('1272509157', (new Type(type: 'timestamp'))->cast(value: [], default: '1272509157'));
    }
    
    public function testCastMethodUsesSpecifiedTypeInstead()
    {
        $this->assertSame(0, (new Type(type: 'string'))->cast(value: 'foo', type: 'int'));
        $this->assertSame(1, (new Type(type: 'string'))->cast(value: 1, type: 'int'));
        $this->assertSame(1, (new Type(type: 'string'))->cast(value: 'foo', type: 'int', default: 1));
    }
}