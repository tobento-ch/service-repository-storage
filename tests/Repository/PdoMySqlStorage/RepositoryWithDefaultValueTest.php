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

namespace Tobento\Service\Repository\Storage\Test\Repository\PdoMySqlStorage;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Repository\Storage\Database\SchemaTableFactory;
use Tobento\Service\Repository\Storage\Test\Mock\ProductRepository;
use Tobento\Service\Storage\PdoMySqlStorage;
use Tobento\Service\Database\Storage\StorageDatabase;
use Tobento\Service\Database\Storage\StorageDatabaseProcessor;
use Tobento\Service\Database\Schema\Table;
use PDO;

/**
 * RepositoryWithDefaultValueTest
 */
class RepositoryWithDefaultValueTest extends \Tobento\Service\Repository\Storage\Test\RepositoryWithDefaultValueTest
{
    public function setUp(): void
    {
        if (! getenv('TEST_TOBENTO_STORAGE_PDO_MYSQL')) {
            $this->markTestSkipped('PdoMySqlStorage tests are disabled');
        }

        $pdo = new PDO(
            dsn: getenv('TEST_TOBENTO_STORAGE_PDO_MYSQL_DSN'),
            username: getenv('TEST_TOBENTO_STORAGE_PDO_MYSQL_USERNAME'),
            password: getenv('TEST_TOBENTO_STORAGE_PDO_MYSQL_PASSWORD'),
            options: [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        );
                
        $this->repository = new ProductRepository(
            storage: new PdoMySqlStorage($pdo),
            table: 'products',
            columns: $this->getColumns(),
        );

        $table = (new SchemaTableFactory())->createTableFromColumns(
            tableName: $this->repository->table(),
            columns: $this->repository->columns(),
        );
        
        $database = new StorageDatabase($this->repository->storage());
        $processor = new StorageDatabaseProcessor();
        $processor->process($table, $database);        
    }

    public function tearDown(): void
    {
        $table = new Table(name: $this->repository->table());
        $table->dropTable();
        
        $database = new StorageDatabase($this->repository->storage());
        $processor = new StorageDatabaseProcessor();
        $processor->process($table, $database);   
    }
}