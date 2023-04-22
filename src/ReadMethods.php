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

use Tobento\Service\Repository\RepositoryReadException;

/**
 * ReadMethods
 */
trait ReadMethods
{
    /**
     * Returns the found entity using the specified id (primary key)
     * or null if none found.
     *
     * @param int|string $id
     * @return null|object
     * @throws RepositoryReadException
     */
    public function findById(int|string $id): null|object
    {
        return $this->createEntityOrNull(
            item: $this->query()->find($id),
        );
    }
    
    /**
     * Returns the found entity using the specified id (primary key)
     * or null if none found.
     *
     * @param int|string ...$ids
     * @return iterable<object>
     * @throws RepositoryReadException
     */
    public function findByIds(int|string ...$ids): iterable
    {
        if (is_null($primaryKey = $this->primaryKey())) {
            return $this->createEntities(items: null);
        }
        
        $query = $this->query();
        
        return $this->createEntities(
            items: $query->whereIn($primaryKey, $ids)->index($primaryKey)->get(),
        );
    }

    /**
     * Returns the found entity using the specified where parameters
     * or null if none found.
     *
     * @param array $where
     * @return null|object
     * @throws RepositoryReadException
     */
    public function findOne(array $where = []): null|object
    {
        $query = $this->query();
        $query = $this->applyWhere($query, $where);
        
        return $this->createEntityOrNull(
            item: $query->first(),
        );
    }

    /**
     * Returns the found entities using the specified parameters.
     *
     * @param array $where Usually where parameters.
     * @param array $orderBy The order by parameters.
     * @param null|int|array $limit The limit e.g. 5 or [5(number), 10(offset)].
     * @return iterable<object>
     * @throws RepositoryReadException
     */
    public function findAll(array $where = [], array $orderBy = [], null|int|array $limit = null): iterable
    {
        $query = $this->query();
        $query = $this->applyWhere($query, $where);
        
        if (!is_null($primaryKey = $this->primaryKey())) {
            $query->index($primaryKey);
        }
        
        $query = $this->applyOrderBy($query, $orderBy);
        $query = $this->applyLimit($query, $limit);

        return $this->createEntities(
            items: $query->get(),
        );
    }
    
    /**
     * Returns the number of items using the specified where parameters.
     *
     * @param array $where
     * @return int
     * @throws RepositoryReadException
     */
    public function count(array $where = []): int
    {
        $query = $this->query();
        $query = $this->applyWhere($query, $where);
        
        return $query->count();
    }
}