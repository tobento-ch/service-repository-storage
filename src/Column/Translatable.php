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

use Tobento\Service\Repository\HasLocales;
use Tobento\Service\Repository\LocalesAware;
use Tobento\Service\Repository\Storage\Attribute\Translations;
use Tobento\Service\Repository\Storage\Attribute\StringTranslations;
use Tobento\Service\Repository\Storage\Attribute\ArrayTranslations;
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
     * Returns the subtype.
     *
     * @return string
     */
    public function getSubtype(): string
    {
        return $this->subtype;
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
        
        // Nullable columns cast null to null, e.g. for records stored before
        // the column was added, so it is read as no translations:
        if (!is_array($value)) {
            $value = [];
        }
        
        $stringTranslations = [];
        $arrayTranslations = [];
        
        foreach($value as $locale => $val) {
            
            if (!is_string($locale) || !in_array($locale, $this->getLocales())) {
                continue;
            }
            
            // Nullable columns cast null to null too, but a translation is never null:
            if ($this->subtype === 'string') {
                $val = $this->getType()->cast(value: $val, type: 'string', default: '');
                $val = $this->readValue(is_string($val) ? $val : '', $attributes, $locale);
                $stringTranslations[$locale] = is_string($val) ? $val : '';
                continue;
            }
            
            $val = $this->getType()->cast(value: $val, type: 'json', default: []);
            $arrayTranslations[$locale] = $this->readValue(is_array($val) ? $val : [], $attributes, $locale);
        }
        
        if ($this->subtype === 'string') {
            return new StringTranslations(
                translations: $stringTranslations,
                locale: $this->getLocale(),
                localeFallbacks: $this->getLocaleFallbacks(),
            );
        }

        return new ArrayTranslations(
            translations: $arrayTranslations,
            locale: $this->getLocale(),
            localeFallbacks: $this->getLocaleFallbacks(),
        );
    }
    
    /**
     * Returns the value read by the reader if any.
     *
     * @param mixed $value
     * @param array $attributes
     * @param string $locale
     * @return mixed
     */
    private function readValue(mixed $value, array $attributes, string $locale): mixed
    {
        if (is_callable($this->reader)) {
            return ($this->reader)($value, $attributes, $locale);
        }
        
        return $value;
    }
    
    /**
     * Write value. Might be used for casting.
     *
     * @param mixed $value
     * @param array $attributes
     * @param string $action The action name that was performed such as 'create' or 'update'.
     * @return mixed
     */
    public function writing(mixed $value, array $attributes, string $action = ''): mixed
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
                $value[$locale] = ($this->writer)($val, $attributes, $action, $locale);
            }
        }

        return $value;
    }
}