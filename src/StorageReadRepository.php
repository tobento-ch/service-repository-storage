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

use Tobento\Service\Repository\ReadRepositoryInterface;
use Tobento\Service\Storage\StorageInterface;
use Tobento\Service\Repository\Storage\Column\ColumnsInterface;
use Tobento\Service\Repository\Storage\Column\ColumnInterface;

/**
 * StorageReadRepository
 */
abstract class StorageReadRepository implements ReadRepositoryInterface, LocalesAware
{
    use StorageMethods;
    use ReadMethods;
    
    /**
     * Create a new StorageRepository.
     *
     * @param StorageInterface $storage
     * @param string $table
     * @param null|iterable<ColumnInterface>|ColumnsInterface $columns
     * @param null|StorageEntityFactoryInterface $entityFactory
     */
    public function __construct(
        protected StorageInterface $storage,
        protected string $table,
        null|iterable|ColumnsInterface $columns = null,
        null|StorageEntityFactoryInterface $entityFactory = null,
    ) {
        $this->columns = $this->processColumns($columns);
        $this->entityFactory = $entityFactory ?: new EntityFactory();
        $this->entityFactory->setColumns($this->columns);
    }
}