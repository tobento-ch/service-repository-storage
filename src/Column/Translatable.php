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

namespace Tobento\Service\Repository\Storage\Column;

use Tobento\Service\Repository\Storage\Attribute\Translations;
use Tobento\Service\Repository\Storage\Attribute\StringTranslations;
use Tobento\Service\Repository\Storage\Attribute\ArrayTranslations;
use Tobento\Service\Repository\Storage\LocalesAware;
use Tobento\Service\Repository\Storage\HasLocales;
use InvalidArgumentException;
use JsonException;

/**
 * Translatable
 */
final class Translatable extends AbstractColumn implements LocalesAware
{
    use HasLocales;
    
    /**
     * Create a new Translatable.
     *
     * @param string $name
     * @param string $subtype
     */
    public function __construct(
        protected string $name,
        protected string $subtype = 'string',
    ) {
        if (!in_array($subtype, ['string', 'array'])) {
            throw new InvalidArgumentException('Subtype must be of type string or array');
        }
        
        $this->type(type: 'json');
    }
    
    /**
     * Create a new instance.
     *
     * @param string $name
     * @param string $subtype
     * @return static
     */
    public static function new(string $name, string $subtype = 'string'): static
    {
        return new static($name, $subtype);
    }
    
    /**
     * Returns true if the attribute is a translatable, otherwise false.
     *
     * @return bool
     */
    public function isTranslatable(): bool
    {
        return true;
    }
    
    /**
     * Read value. Might be used for casting.
     *
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function reading(mixed $value, array $attributes): mixed
    {
        $value = $this->getType()->cast(value: $value, type: 'json', default: []);
        
        foreach($value as $locale => $val) {
            
            if (!in_array($locale, $this->getLocales())) {
                unset($value[$locale]);
                continue;
            }
            
            if ($this->subtype === 'string') {
                $val = $this->getType()->cast(value: $val, type: 'string', default: '');
            } else {
                $val = $this->getType()->cast(value: $val, type: 'json', default: []);
            }
            
            if (is_callable($this->reader)) {
                $value[$locale] = ($this->reader)($val, $attributes, $locale);
            } else {
                $value[$locale] = $val;
            }
        }
        
        if ($this->subtype === 'string') {
            return new StringTranslations(
                translations: $value,
                locale: $this->getLocale(),
                localeFallbacks: $this->getLocaleFallbacks(),
            );
        }

        return new ArrayTranslations(
            translations: $value,
            locale: $this->getLocale(),
            localeFallbacks: $this->getLocaleFallbacks(),
        );
    }
    
    /**
     * Write value. Might be used for casting.
     *
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function writing(mixed $value, array $attributes): mixed
    {
        if (is_string($value)) {
            $value = [$this->getLocale() => $value];
        }
        
        if (!is_array($value)) {
            return [];
        }
        
        foreach($value as $locale => $val) {
            
            if (!in_array($locale, $this->getLocales())) {
                unset($value[$locale]);
                continue;
            }
            
            if (is_callable($this->writer)) {
                $value[$locale] = ($this->writer)($val, $attributes, $locale);
            }
        }

        return $value;
    }
}