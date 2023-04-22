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
use Tobento\Service\Repository\Storage\Test\Mock\ProductWriteRepository;
use Tobento\Service\Storage\PdoMySqlStorage;
use Tobento\Service\Storage\ItemInterface;
use Tobento\Service\Storage\ItemsInterface;
use Tobento\Service\Database\Storage\StorageDatabase;
use Tobento\Service\Database\Storage\StorageDatabaseProcessor;
use Tobento\Service\Database\Schema\Table;
use PDO;

/**
 * WriteRepositoryTest
 */
class WriteRepositoryTest extends \Tobento\Service\Repository\Storage\Test\WriteRepositoryTest
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
                
        $this->repository = new ProductWriteRepository(
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
    
    public function testUpdateByIdMethod()
    {
        $created = $this->repository->create(['sku' => 'scissors']);
        
        $updated = $this->repository->updateById(id: 1, attributes: ['price' => 2.5]);
        
        $this->assertInstanceof(ItemInterface::class, $updated);
        $this->assertSame(1, $updated->get('id'));
        $this->assertSame(null, $updated->get('price'));
    }
    
    public function testUpdateByIdMethodThrowsRepositoryUpdateExceptionIfNotExists()
    {
        $this->assertTrue(true);
    }
    
    public function testUpdateMethod()
    {
        $created = $this->repository->create(['sku' => 'pen']);
        $created = $this->repository->create(['sku' => 'scissors']);
        
        $updated = $this->repository->update(where: ['sku' => 'pen'], attributes: ['price' => 2.5]);
        
        $this->assertInstanceof(ItemsInterface::class, $updated);
        $this->assertSame(1, $updated->count());
        $this->assertSame(null, $updated->first()?->get('sku'));
    }
    
    public function testDeleteByIdMethodThrowsRepositoryDeleteExceptionIfNotExists()
    {
        $this->assertTrue(true);
    }
    
    public function testDeleteByIdMethod()
    {
        $created = $this->repository->create(['sku' => 'pen']);
        $created = $this->repository->create(['sku' => 'scissors']);
        
        $deleted = $this->repository->deleteById(id: 2);
        
        $this->assertInstanceof(ItemInterface::class, $deleted);
        
        $this->assertSame(2, $deleted->get('id'));
    }
    
    public function testDeleteMethod()
    {
        $created = $this->repository->create(['sku' => 'pen']);
        $created = $this->repository->create(['sku' => 'scissors']);
        
        $deleted = $this->repository->delete(where: ['sku' => 'scissors']);
        
        $this->assertInstanceof(ItemsInterface::class, $deleted);
        $this->assertSame(1, $deleted->count());
        
        $this->assertSame(null, $deleted->first()?->get('sku'));
    }
}