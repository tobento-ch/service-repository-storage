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
use Tobento\Service\Repository\Storage\Attribute\ArrayTranslations;
use Tobento\Service\Support\Arrayable;
use Tobento\Service\Support\Jsonable;
use Tobento\Service\Collection\Collection;
use IteratorAggregate;
use Stringable;

/**
 * ArrayTranslationsTest
 */
class ArrayTranslationsTest extends TestCase
{
    public function testImplementsInterfaces()
    {
        $translations = new ArrayTranslations(
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
        $translations = new ArrayTranslations(
            translations: ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            locale: 'en',
        );
        
        $this->assertSame(['color' => 'red'], $translations->get());
    }
    
    public function testGetMethodSpecificLocale()
    {
        $translations = new ArrayTranslations(
            translations: ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            locale: 'en',
        );
        
        $this->assertSame(['color' => 'rot'], $translations->get(locale: 'de'));
    }
    
    public function testGetMethodSpecificLocaleUsesDefaultIfFallbackLocaleDoesNotExist()
    {
        $translations = new ArrayTranslations(
            translations: ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            locale: 'en',
        );
        
        $this->assertSame(['color' => 'rouge'], $translations->get(locale: 'fr', default: ['color' => 'rouge']));
    }
    
    public function testGetMethodSpecificLocaleUsesFallbackLocale()
    {
        $translations = new ArrayTranslations(
            translations: ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            locale: 'en',
            localeFallbacks: ['fr' => 'en'],
        );
        
        $this->assertSame(['color' => 'red'], $translations->get(locale: 'fr', default: ['color' => 'rouge']));
    }
    
    public function testGetMethodWithKey()
    {
        $translations = new ArrayTranslations(
            translations: ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            locale: 'en',
        );
        
        $this->assertSame('red', $translations->get(key: 'color'));
    }
    
    public function testGetMethodWithKeySpecificLocale()
    {
        $translations = new ArrayTranslations(
            translations: ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            locale: 'en',
        );
        
        $this->assertSame('rot', $translations->get(locale: 'de', key: 'color'));
    }
    
    public function testGetMethodWithKeySpecificLocaleUsesDefaultIfFallbackLocaleDoesNotExist()
    {
        $translations = new ArrayTranslations(
            translations: ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            locale: 'en',
        );
        
        $this->assertSame(
            'rouge',
            $translations->get(locale: 'fr', key: 'color', default: 'rouge')
        );
    }
    
    public function testGetMethodWithKeySpecificLocaleUsesFallbackLocale()
    {
        $translations = new ArrayTranslations(
            translations: ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            locale: 'en',
            localeFallbacks: ['fr' => 'en'],
        );
        
        $this->assertSame(
            'red',
            $translations->get(locale: 'fr', key: 'color', default: 'rouge')
        );
    }
    
    public function testHasMethod()
    {
        $translations = new ArrayTranslations(
            translations: ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            locale: 'en',
        );
        
        $this->assertTrue($translations->has(locale: 'en'));
        $this->assertTrue($translations->has(locale: 'de'));
        $this->assertFalse($translations->has(locale: 'fr'));
    }
    
    public function testHasMethodWithKey()
    {
        $translations = new ArrayTranslations(
            translations: ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            locale: 'en',
        );
        
        $this->assertTrue($translations->has(locale: 'en', key: 'color'));
        $this->assertTrue($translations->has(locale: 'de', key: 'color'));
        $this->assertFalse($translations->has(locale: 'en', key: 'foo'));
        $this->assertFalse($translations->has(locale: 'fr', key: 'color'));
    }
    
    public function testAllMethod()
    {
        $translations = new ArrayTranslations(
            translations: ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            locale: 'en',
        );
        
        $this->assertSame(
            ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            $translations->all()
        );
    }
    
    public function testToStringMethod()
    {
        $translations = new ArrayTranslations(
            translations: ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            locale: 'en',
        );
        
        $this->assertSame(
            '{"en":{"color":"red"},"de":{"color":"rot"}}',
            (string)$translations
        );
    }
    
    public function testCollectionMethod()
    {
        $translations = new ArrayTranslations(
            translations: ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            locale: 'en',
        );
        
        $this->assertInstanceof(Collection::class, $translations->collection());
        
        $this->assertSame(
            ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            $translations->collection()->all()
        );
    }
    
    public function testGetIteratorMethod()
    {
        $translations = new ArrayTranslations(
            translations: ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            locale: 'en',
        );
        
        $iterated = [];
        
        foreach($translations as $locale => $value) {
            $iterated[$locale] = $value;
        }
        
        $this->assertSame(
            ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            $iterated
        );
    }
    
    public function testToArrayMethod()
    {
        $translations = new ArrayTranslations(
            translations: ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            locale: 'en',
        );
        
        $this->assertSame(
            ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            $translations->toArray()
        );
    }
    
    public function testToJsonMethod()
    {
        $translations = new ArrayTranslations(
            translations: ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
            locale: 'en',
        );
        
        $this->assertSame(
            '{"en":{"color":"red"},"de":{"color":"rot"}}',
            $translations->toJson()
        );
    }
}