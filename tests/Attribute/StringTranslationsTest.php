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

namespace Tobento\Service\Repository\Storage\Test\Attribute;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Repository\Storage\Attribute\StringTranslations;
use Tobento\Service\Support\Arrayable;
use Tobento\Service\Support\Jsonable;
use Tobento\Service\Collection\Collection;
use IteratorAggregate;
use Stringable;

/**
 * StringTranslationsTest
 */
class StringTranslationsTest extends TestCase
{
    public function testImplementsInterfaces()
    {
        $translations = new StringTranslations(
            translations: [],
            locale: 'en',
        );
        
        $this->assertInstanceof(Arrayable::class, $translations);
        $this->assertInstanceof(Jsonable::class, $translations);
        $this->assertInstanceof(Stringable::class, $translations);
        $this->assertInstanceof(IteratorAggregate::class, $translations);
    }
    
    public function testGetMethod()
    {
        $translations = new StringTranslations(
            translations: ['en' => 'red', 'de' => 'rot'],
            locale: 'en',
        );
        
        $this->assertSame('red', $translations->get());
    }
    
    public function testGetMethodSpecificLocale()
    {
        $translations = new StringTranslations(
            translations: ['en' => 'red', 'de' => 'rot'],
            locale: 'en',
        );
        
        $this->assertSame('rot', $translations->get(locale: 'de'));
    }
    
    public function testGetMethodSpecificLocaleUsesDefaultIfFallbackLocaleDoesNotExist()
    {
        $translations = new StringTranslations(
            translations: ['en' => 'red', 'de' => 'rot'],
            locale: 'en',
        );
        
        $this->assertSame('rouge', $translations->get(locale: 'fr', default: 'rouge'));
    }
    
    public function testGetMethodSpecificLocaleUsesFallbackLocale()
    {
        $translations = new StringTranslations(
            translations: ['en' => 'red', 'de' => 'rot'],
            locale: 'en',
            localeFallbacks: ['fr' => 'en'],
        );
        
        $this->assertSame('red', $translations->get(locale: 'fr', default: 'rouge'));
    }
    
    public function testHasMethod()
    {
        $translations = new StringTranslations(
            translations: ['en' => 'red', 'de' => 'rot'],
            locale: 'en',
        );
        
        $this->assertTrue($translations->has(locale: 'en'));
        $this->assertTrue($translations->has(locale: 'de'));
        $this->assertFalse($translations->has(locale: 'fr'));
    }
    
    public function testAllMethod()
    {
        $translations = new StringTranslations(
            translations: ['en' => 'red', 'de' => 'rot'],
            locale: 'en',
        );
        
        $this->assertSame(
            ['en' => 'red', 'de' => 'rot'],
            $translations->all()
        );
    }
    
    public function testToStringMethod()
    {
        $translations = new StringTranslations(
            translations: ['en' => 'red', 'de' => 'rot'],
            locale: 'en',
        );
        
        $this->assertSame('red', (string)$translations);
    }
    
    public function testCollectionMethod()
    {
        $translations = new StringTranslations(
            translations: ['en' => 'red', 'de' => 'rot'],
            locale: 'en',
        );
        
        $this->assertInstanceof(Collection::class, $translations->collection());
        
        $this->assertSame(
            ['en' => 'red', 'de' => 'rot'],
            $translations->collection()->all()
        );
    }
    
    public function testGetIteratorMethod()
    {
        $translations = new StringTranslations(
            translations: ['en' => 'red', 'de' => 'rot'],
            locale: 'en',
        );
        
        $iterated = [];
        
        foreach($translations as $locale => $value) {
            $iterated[$locale] = $value;
        }
        
        $this->assertSame(
            ['en' => 'red', 'de' => 'rot'],
            $iterated
        );
    }
    
    public function testToArrayMethod()
    {
        $translations = new StringTranslations(
            translations: ['en' => 'red', 'de' => 'rot'],
            locale: 'en',
        );
        
        $this->assertSame(
            ['en' => 'red', 'de' => 'rot'],
            $translations->toArray()
        );
    }
    
    public function testToJsonMethod()
    {
        $translations = new StringTranslations(
            translations: ['en' => 'red', 'de' => 'rot'],
            locale: 'en',
        );
        
        $this->assertSame(
            '{"en":"red","de":"rot"}',
            $translations->toJson()
        );
    }
}