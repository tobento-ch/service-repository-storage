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

namespace Tobento\Service\Repository\Storage\Attribute;

use Tobento\Service\Support\Arrayable;
use Tobento\Service\Support\Jsonable;
use Tobento\Service\Collection\Arr;
use Tobento\Service\Collection\Collection;
use IteratorAggregate;
use Stringable;
use Generator;

/**
 * ArrayTranslations
 */
final class ArrayTranslations implements Arrayable, Jsonable, Stringable, IteratorAggregate
{
    /**
     * Create a new ArrayTranslations.
     *
     * @param array $translations
     * @param string $locale
     * @param array<string, string> $localeFallbacks
     */
    public function __construct(
        protected array $translations,
        protected string $locale,
        protected array $localeFallbacks = [],
    ) {}
    
    /**
     * Returns the translated value.
     *
     * @param null|string $locale
     * @param null|string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(null|string $locale = null, null|string $key = null, mixed $default = null): mixed
    {
        if (is_null($locale)) {
            $locale = $this->locale;
        }
        
        if (! $this->has($locale)) {
            $locale = $this->localeFallbacks[$locale] ?? $locale;
        }
        
        if (!is_null($key)) {
            return Arr::get($this->translations, $locale.'.'.$key, $default);
        }
        
        return $this->translations[$locale] ?? $default;
    }
    
    /**
     * Returns true if has translation, otherwise false.
     *
     * @param string $locale
     * @param null|string $key
     * @return bool
     */
    public function has(string $locale, null|string $key = null): bool
    {
        if (!is_null($key)) {
            return Arr::has($this->translations, $locale.'.'.$key);
        }
        
        return array_key_exists($locale, $this->translations);
    }
    
    /**
     * Returns all translations.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->translations;
    }
    
    /**
     * To String.
     *
     * @return string
     */
    public function __toString(): string
    {
        $value = $this->get();
        
        return is_string($value) ? $value : $this->toJson();
    }
    
    /**
     * Returns a new Collection with the translations.
     *
     * @return Collection
     */
    public function collection(): Collection
    {
        return new Collection($this->translations);
    }
    
    /**
     * Returns an iterator for the translations.
     *
     * @return Generator
     */
    public function getIterator(): Generator
    {
        foreach($this->translations as $key => $value) {
            yield $key => $value;
        }
    }
    
    /**
     * Object to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->collection()->toArray();
    }
    
    /**
     * Object to json.
     *
     * @param int $options
     * @return string
     */
    public function toJson(int $options = 0): string
    {
        return $this->collection()->toJson();
    }
}