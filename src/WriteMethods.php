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

use Tobento\Service\Repository\RepositoryCreateException;
use Tobento\Service\Repository\RepositoryUpdateException;
use Tobento\Service\Repository\RepositoryDeleteException;
use Tobento\Service\Storage\ItemInterface;

/**
 * WriteMethods
 */
trait WriteMethods
{
    /**
     * Create an entity.
     *
     * @param array $attributes
     * @return object The created entity.
     * @throws RepositoryCreateException
     */
    public function create(array $attributes): object
    {
        $attributes = $this->columns()->processWriting($attributes);
        
        return $this->entityFactory()->createEntityFromStorageItem(
            item: $this->query()->insert($attributes),
        );
    }
    
    /**
     * Update an entity by id.
     *
     * @param string|int $id
     * @param array $attributes The attributes to update the entity.
     * @return object The updated entity.
     * @throws RepositoryUpdateException
     */
    public function updateById(string|int $id, array $attributes): object
    {
        if (is_null($primaryKey = $this->primaryKey())) {
            throw new RepositoryUpdateException(
                message: 'Storage table has no primary key',
                attributes: $attributes,
                id: $id,
            );
        }
        
        $attributes = $this->columns()->processWriting($attributes);
        
        $updatedItem = $this->query()
            ->where($primaryKey, '=', $id)
            ->update($attributes)
            ->first();
        
        if (is_null($updatedItem)) {
            
            if ($this->storage()->supportsReturningItems(method: 'update')) {
                throw new RepositoryUpdateException(
                    message: sprintf('Entity with the id [%s] not found', (string)$id),
                    attributes: $attributes,
                    id: $id,
                );
            }
            
            $updatedItem = [];
            $updatedItem[$primaryKey] = $id;
        }
        
        return $this->entityFactory()->createEntityFromArray(
            attributes: $updatedItem,
        );
    }
    
    /**
     * Update entities.
     *
     * @param array $where The where parameters.
     * @param array $attributes The attributes to update the entities.
     * @return iterable<object> The updated entities.
     * @throws RepositoryUpdateException
     */
    public function update(array $where, array $attributes): iterable
    {
        $attributes = $this->columns()->processWriting($attributes);
        $query = $this->query();
        $query = $this->applyWhere($query, $where);
        
        return $this->createEntities(
            items: $query->update($attributes),
        );
    }
    
    /**
     * Delete an entity by id.
     *
     * @param string|int $id
     * @return object The deleted entity.
     * @throws RepositoryDeleteException
     */
    public function deleteById(string|int $id): object
    {
        if (is_null($primaryKey = $this->primaryKey())) {
            throw new RepositoryDeleteException(
                message: 'Storage table has no primary key',
                id: $id,
            );
        }
        
        $deletedItem = $this->query()
            ->where($primaryKey, '=', $id)
            ->delete()
            ->first();
        
        if (is_null($deletedItem)) {
            
            if ($this->storage()->supportsReturningItems(method: 'delete')) {
                throw new RepositoryDeleteException(
                    message: sprintf('Entity with the id [%s] not found', (string)$id),
                    id: $id,
                );
            }
            
            $deletedItem = [];
            $deletedItem[$primaryKey] = $id;
        }
        
        return $this->entityFactory()->createEntityFromArray(
            attributes: $deletedItem,
        );
    }
    
    /**
     * Delete entities.
     *
     * @param array $where The where parameters.
     * @return iterable<object> The deleted entities.
     * @throws RepositoryDeleteException
     */
    public function delete(array $where): iterable
    {
        $query = $this->query();
        $query = $this->applyWhere($query, $where);
        
        return $this->createEntities(
            items: $query->delete()
        );
    }
}