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

namespace Tobento\Service\Repository\Storage\Migration;

use Tobento\Service\Repository\Storage\StorageRepository;
use Tobento\Service\Repository\Storage\StorageReadRepository;
use Tobento\Service\Repository\Storage\StorageWriteRepository;
use Tobento\Service\Repository\Storage\Database\SchemaTableFactory;
use Tobento\Service\Migration\ActionInterface;
use Tobento\Service\Migration\ActionFailedException;
use Tobento\Service\Database\Storage\StorageDatabase;
use Tobento\Service\Database\Storage\StorageDatabaseProcessor;
use Tobento\Service\Database\Processor\ProcessException;

/**
 * RepositoryDeleteAction
 */
class RepositoryDeleteAction implements ActionInterface
{
    /**
     * Create a new RepositoryDeleteAction.
     *
     * @param StorageRepository|StorageReadRepository|StorageWriteRepository $repository
     * @param string $description A description of the action.
     */
    public function __construct(
        protected StorageRepository|StorageReadRepository|StorageWriteRepository $repository,
        protected string $description = '',
    ) {}
    
    /**
     * Process the action.
     *
     * @return void
     * @throws ActionFailedException
     */
    public function process(): void
    {
        $table = (new SchemaTableFactory())->createTableFromColumns(
            tableName: $this->repository->table(),
            columns: $this->repository->columns(),
        );
        
        $database = new StorageDatabase($this->repository->storage());
        $processor = new StorageDatabaseProcessor();
        
        try {
            $table->dropTable();
            $processor->process(table: $table, database: $database);
        } catch (ProcessException $e) {
            throw new ActionFailedException(
                $this,
                'Repository Action Failed!',
                0,
                $e
            );
        }
    }
    
    /**
     * Returns a name of the action.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->repository->table();
    }
 
    /**
     * Returns a description of the action.
     *
     * @return string
     */
    public function description(): string
    {
        return $this->description;
    }
    
    /**
     * Returns the processed data information.
     *
     * @return array<array-key, string>
     */
    public function processedDataInfo(): array
    {
        return [];
    }
}