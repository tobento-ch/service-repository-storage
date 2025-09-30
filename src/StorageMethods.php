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

use Tobento\Service\Repository\Storage\Column\ColumnsInterface;
use Tobento\Service\Repository\Storage\Column\Columns;
use Tobento\Service\Repository\Storage\Column\ColumnInterface;
use Tobento\Service\Storage\StorageInterface;
use Tobento\Service\Storage\ItemsInterface;
use Tobento\Service\Storage\ItemInterface;
use Tobento\Service\Storage\Items;
use Tobento\Service\Storage\Tables\Column;
use Tobento\Service\Iterable\Iter;

/**
 * StorageMethods
 */
trait StorageMethods
{
    use HasLocales;
    
    /**
     * @var ColumnsInterface
     */
    protected ColumnsInterface $columns;
    
    /**
     * @var StorageEntityFactoryInterface
     */
    protected StorageEntityFactoryInterface $entityFactory;
    
    /**
     * Returns the storage.
     *
     * @return StorageInterface
     */
    public function storage(): StorageInterface
    {
        return $this->storage;
    }
    
    /**
     * Returns the storage with the table specified
     * to perform a query.
     *
     * @return StorageInterface
     */
    public function query(): StorageInterface
    {
        return $this->storage()->table($this->table);
    }
    
    /**
     * Returns the table.
     *
     * @return string
     */
    public function table(): string
    {
        return $this->table;
    }
    
    /**
     * Returns the columns.
     *
     * @return ColumnsInterface
     */
    public function columns(): ColumnsInterface
    {
        return $this->columns;
    }
    
    /**
     * Returns the entity factory.
     *
     * @return StorageEntityFactoryInterface
     */
    public function entityFactory(): StorageEntityFactoryInterface
    {
        return $this->entityFactory;
    }
    
    /**
     * Sets the locale.
     *
     * @param string $locale
     * @return static $this
     */
    public function locale(string $locale): static
    {
        $this->locale = $locale;
        
        foreach($this->columns() as $column) {
            if ($column instanceof LocalesAware) {
                $column->locale($locale);
            }
        }

        return $this;
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
        
        foreach($this->columns() as $column) {
            if ($column instanceof LocalesAware) {
                $column->locales(...$locales);
            }
        }
        
        return $this;
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
        
        foreach($this->columns() as $column) {
            if ($column instanceof LocalesAware) {
                $column->localeFallbacks($localeFallbacks);
            }
        }
        
        return $this;
    }
    
    /**
     * Process columns.
     *
     * @param null|iterable<ColumnInterface>|ColumnsInterface $columns
     * @return ColumnsInterface
     */
    protected function processColumns(null|iterable|ColumnsInterface $columns): ColumnsInterface
    {
        if (is_null($columns)) {
            $columns = $this->configureColumns();
        }
        
        if (! $columns instanceof ColumnsInterface) {
            $columns = Iter::toArray($columns);
            $columns = new Columns(...$columns);
        }
        
        // skip add table if no columns:
        if ($columns->empty()) {
            return $columns;
        }
        
        // add storage table:
        $this->storage()->tables()->add(
            $this->table,
            $columns->storable()->column('name'),
            $columns->primary()?->name()
        );
        
        return $columns;
    }
    
    /**
     * Returns the configured columns.
     *
     * @return iterable<ColumnInterface>|ColumnsInterface
     */
    protected function configureColumns(): iterable|ColumnsInterface
    {
        return [];
    }
    
    /**
     * Returns the primary key or null if none.
     *
     * @return null|string
     */
    protected function primaryKey(): null|string
    {
        return $this->storage()->tables()->getPrimaryKey($this->table());
    }
    
    /**
     * Applies where parameters to the storage. 
     *
     * @param StorageInterface $storage
     * @param array $where
     * @return StorageInterface
     */
    protected function applyWhere(StorageInterface $storage, array $where): StorageInterface
    {
        $translatableColumns = $this->columns->translatable()->column('name');
        
        foreach($where as $column => $value) {
            if (!is_string($column) && is_array($value)) {
                $boolean = $this->extractFirstBooleanFromWhereColumns($value);
                
                if ($boolean === 'or') {
                    $storage->orWhere(function(StorageInterface $query) use ($value): void {
                        $this->applyWhere($query, $value);
                    });
                    continue;
                }
                
                $storage->where(function(StorageInterface $query) use ($value): void {
                    $this->applyWhere($query, $value);
                });
                continue;
            }
            
            if (!is_string($column)) {
                continue;
            }
            
            $column = new Column($column);
            
            // handle translation column:
            if (in_array($column->name(), $translatableColumns)) {
                $column = $this->assignLocaleToColumn($column);
            }
            
            if (is_array($value) && !empty($value)) {
                foreach($value as $operator => $v) {
                    
                    [$operator, $boolean] = $this->parseOperator($operator, $v);
                    
                    // for ['null'] e.g.
                    if (is_null($operator)) {
                        $operator = is_string($v) ? $v : 'null';
                        $operator = $operator === 'or null' ? 'null' : $operator;
                        $operator = $operator === 'or not null' ? 'not null' : $operator;
                        $v = null;
                    }
                    
                    // check for like operator with multiple values:
                    if (
                        is_array($v)
                        && in_array($operator, ['like', 'or like', 'not like', 'or not like'])
                    ) {
                        foreach($v as $mv) {
                            $this->mapWhereClause($storage, $column->column(), $operator, $boolean, $mv);
                        }
                        continue;
                    }

                    $this->mapWhereClause($storage, $column->column(), $operator, $boolean, $v);
                }
            } else {
                $storage->where($column->column(), '=', $value);
            }
        }
        
        //echo '<pre>'; print_r($storage->table('users')->getQuery()); exit;
        return $storage;
    }

    /**
     * Extract the first boolean from the where columns. 
     *
     * @param array $where
     * @return string
     */
    protected function extractFirstBooleanFromWhereColumns(array $where): string
    {
        $columnName = array_key_first($where);

        if (is_null($columnName)) {
            return 'and';
        }
        
        $clause = $where[$columnName];
        
        if (is_array($clause) && !empty($clause)) {
            foreach(array_keys($clause) as $operator) {
                $boolean = $this->parseOperator($operator)[1] ?? 'and';
                return $boolean;
            }
        }
        
        return 'and';
    }
    
    /**
     * Parse operator. 
     *
     * @param mixed $operator
     * @return array
     */
    protected function parseOperator(mixed $operator, mixed $value = null): array
    {
        if (is_int($operator)) {
            if (is_string($value) && str_starts_with($value, 'or ')) {
                return [null, 'or'];
            }
            return [null, 'and'];
        }

        if (!is_string($operator)) {
            return ['=', 'and'];
        }
        
        if (str_starts_with($operator, 'or ')) {
            return [substr($operator, 3), 'or'];
        }
        
        return [$operator, 'and'];
    }
    
    /**
     * Assignes locale to column. 
     *
     * @param Column $column
     * @return Column
     * @psalm-suppress DuplicateArrayKey
     */
    protected function assignLocaleToColumn(Column $column): Column
    {
        if (is_null($column->jsonSegments())) {
            return $column->withJsonSegments([$this->getLocale()]);
        }
        
        if (in_array($column->jsonSegments()[0], $this->getLocales())) {
            return $column;
        }
        
        return $column->withJsonSegments([$this->getLocale(), ...($column->jsonSegments() ?? [])]);
    }
    
    /**
     * Maps to storage where clause. 
     *
     * @param StorageInterface $storage
     * @param string $column
     * @param string $operator
     * @param string $boolean
     * @param mixed $value
     * @return void
     */
    protected function mapWhereClause(
        StorageInterface $storage,
        string $column,
        string $operator,
        string $boolean,
        mixed $value,
    ): void {
        switch ($operator) {
            case 'between':
                if (!is_array($value)) {
                    $value = [];
                }
                
                if ($boolean === 'or') {
                    $storage->orWhereBetween($column, $value);
                } else {
                    $storage->whereBetween($column, $value);
                }
                return;
            case 'not between':
                if (!is_array($value)) {
                    $value = [];
                }
                
                if ($boolean === 'or') {
                    $storage->orWhereNotBetween($column, $value);
                } else {
                    $storage->whereNotBetween($column, $value);
                }
                return;
            case 'null':
                if ($boolean === 'or') {
                    $storage->orWhereNull($column);
                } else {
                    $storage->whereNull($column);
                }
                return;
            case 'not null':
                if ($boolean === 'or') {
                    $storage->orWhereNotNull($column);
                } else {
                    $storage->whereNotNull($column);
                }
                return;
            case 'in':
                if ($boolean === 'or') {
                    $storage->orWhereIn($column, $value);
                } else {
                    $storage->whereIn($column, $value);
                }
                return;
            case 'not in':
                if ($boolean === 'or') {
                    $storage->orWhereNotIn($column, $value);
                } else {
                    $storage->whereNotIn($column, $value);
                }
                return;
            case 'contains':
                if ($boolean === 'or') {
                    $storage->orWhereJsonContains($column, $value);
                } else {
                    $storage->whereJsonContains($column, $value);
                }
                return;
            case 'contains key':
                if ($boolean === 'or') {
                    $storage->orWhereJsonContainsKey($column);
                } else {
                    $storage->whereJsonContainsKey($column);
                }
                return;
        }
        
        // storage will verify operators, so no need to!
        if ($boolean === 'or') {
            $storage->orWhere($column, $operator, $value);
        } else {
            $storage->where($column, $operator, $value);
        }
    }
    
    /**
     * Applies order by parameters to the storage. 
     *
     * @param StorageInterface $storage
     * @param array $orderBy
     * @return StorageInterface
     */
    protected function applyOrderBy(StorageInterface $storage, array $orderBy): StorageInterface
    {
        foreach($orderBy as $column => $value) {
            if (is_array($value)) {
                foreach($value as $v) {
                    $storage->order($column, $v);
                }
            } else {
                $storage->order($column, $value);
            }
        }
        
        return $storage;
    }
    
    /**
     * Applies limit parameter to the storage. 
     *
     * @param StorageInterface $storage
     * @param null|int|array $limit
     * @return StorageInterface
     */
    protected function applyLimit(StorageInterface $storage, null|int|array $limit): StorageInterface
    {
        if (is_null($limit)) {
            return $storage;
        }
        
        if (is_int($limit)) {
            return $storage->limit(number: $limit);
        }
        
        $number = $limit[0] ?? null;
        $number = is_numeric($number) ? (int)$number : null;
        
        $offset = $limit[1] ?? 0;
        $offset = is_numeric($offset) ? (int)$offset : 0;
        
        return $storage->limit(number: $number, offset: $offset);
    }
    
    /**
     * Returns the created entity or null.
     *
     * @param null|ItemInterface $item
     * @return null|object
     */
    protected function createEntityOrNull(null|ItemInterface $item): null|object
    {
        if (is_null($item)) {
            return null;
        }
        
        return $this->entityFactory()->createEntityFromStorageItem(item: $item);
    }
    
    /**
     * Returns the created entities.
     *
     * @param null|ItemsInterface $items
     * @return iterable<object> The created entities.
     */
    protected function createEntities(null|ItemsInterface $items): iterable
    {
        if (is_null($items)) {
            return new Items();
        }
        
        return $this->entityFactory()->createEntitiesFromStorageItems(items: $items);
    }
}