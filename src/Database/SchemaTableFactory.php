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

namespace Tobento\Service\Repository\Storage\Database;

use Tobento\Service\Database\Schema\Table;
use Tobento\Service\Database\Schema\TableFactoryInterface;
use Tobento\Service\Database\Schema\TableFactory;
use Tobento\Service\Database\Schema\ColumnFactoryInterface;
use Tobento\Service\Database\Schema\ColumnFactory;
use Tobento\Service\Database\Schema\IndexFactoryInterface;
use Tobento\Service\Database\Schema\IndexFactory;
use Tobento\Service\Repository\Storage\Column\ColumnsInterface;

/**
 * SchemaTableFactory
 */
class SchemaTableFactory
{
    /**
     * @var TableFactoryInterface
     */
    protected TableFactoryInterface $tableFactory;
    
    /**
     * @var ColumnFactoryInterface
     */
    protected ColumnFactoryInterface $columnFactory;
    
    /**
     * @var IndexFactoryInterface
     */
    protected IndexFactoryInterface $indexFactory;    
    
    /**
     * Create a new SchemaTableFactory.
     *
     * @param null|TableFactoryInterface $tableFactory
     * @param null|ColumnFactoryInterface $columnFactory
     * @param null|IndexFactoryInterface $indexFactory
     */
    public function __construct(
        null|TableFactoryInterface $tableFactory = null,
        null|ColumnFactoryInterface $columnFactory = null,
        null|IndexFactoryInterface $indexFactory = null,
    ) {
        $this->tableFactory = $tableFactory ?: new TableFactory();
        $this->columnFactory = $columnFactory ?: new ColumnFactory();
        $this->indexFactory = $indexFactory ?: new IndexFactory();
    }
    
    /**
     * Create tables from the repository.
     *
     * @param StorageRepository|StorageReadRepository|StorageWriteRepository $repository
     * @return Table
     */
    /*public function createTableFromRepository(
        StorageRepository|StorageReadRepository|StorageWriteRepository $repository
    ): Table {
        return $this->createTableFromColumns(
            tableName: $repository->table(),
            columns: $repository->columns(),
        );
    }*/
    
    /**
     * Create a new Table from the specified table name and columns.
     *
     * @param string $tableName
     * @param ColumnsInterface $columns
     * @return Table
     */
    public function createTableFromColumns(string $tableName, ColumnsInterface $columns): Table
    {
        $table = $this->tableFactory->createTable($tableName);
        
        foreach($columns->storable() as $col)
        {
            $params = $col->getType()->parameters();
            $params['name'] = $col->name();
            
            $column = $this->columnFactory->createColumnFromArray($params);
            
            $table->addColumn($column);
            
            if (is_array($index = $col->getType()->get('index')))
            {
                $index = $this->indexFactory->createIndexFromArray($index);
                $table->addIndex($index);
            }
        }
        
        return $table;
    }
}