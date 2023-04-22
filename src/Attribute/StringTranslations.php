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
use Tobento\Service\Support\Arr;
use Tobento\Service\Collection\Collection;
use IteratorAggregate;
use Stringable;
use Generator;

/**
 * StringTranslations
 */
final class StringTranslations implements Arrayable, Jsonable, Stringable, IteratorAggregate
{
    /**
     * Create a new StringTranslations.
     *
     * @param array<string, string> $translations
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
     * @param mixed $default
     * @return mixed
     */
    public function get(null|string $locale = null, string $default = ''): string
    {
        if (is_null($locale)) {
            $locale = $this->locale;
        }
        
        if (! $this->has($locale)) {
            $locale = $this->localeFallbacks[$locale] ?? $locale;
        }
        
        return $this->translations[$locale] ?? $default;
    }
    
    /**
     * Returns true if has translation, otherwise false.
     *
     * @param string $locale
     * @return bool
     */
    public function has(string $locale): bool
    {
        return array_key_exists($locale, $this->translations);
    }
    
    /**
     * Returns all translations.
     *
     * @return array<string, string>
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
        return $this->get();
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