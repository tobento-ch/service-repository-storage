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

namespace Tobento\Service\Repository\Storage;

/**
 * HasLocales
 */
trait HasLocales
{
    /**
     * @var string
     */
    protected string $locale = 'en';
    
    /**
     * @var array<int, string>
     */
    protected array $locales = ['en'];
    
    /**
     * @var array<string, string>
     */
    protected array $localeFallbacks = [];
    
    /**
     * Sets the locale.
     *
     * @param string $locale
     * @return static $this
     */
    public function locale(string $locale): static
    {
        $this->locale = $locale;
        return $this;
    }
    
    /**
     * Returns the locale.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }
    
    /**
     * Sets the locales.
     *
     * @param string ...$locales
     * @return static $this
     */
    public function locales(string ...$locales): static
    {
        $this->locales = $locales;
        return $this;
    }
    
    /**
     * Returns the locales.
     *
     * @return array
     */
    public function getLocales(): array
    {
        return $this->locales;
    }
    
    /**
     * Sets the locale fallbacks.
     *
     * @param array<string, string> $localeFallbacks
     * @return static $this
     */
    public function localeFallbacks(array $localeFallbacks): static
    {
        $this->localeFallbacks = $localeFallbacks;
        return $this;
    }
    
    /**
     * Returns the locale fallbacks.
     *
     * @return array<string, string>
     */
    public function getLocaleFallbacks(): array
    {
        return $this->localeFallbacks;
    }
}