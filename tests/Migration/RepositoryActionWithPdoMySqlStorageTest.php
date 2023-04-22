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

namespace Tobento\Service\Repository\Storage\Test\Migration;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Repository\Storage\Migration\RepositoryAction;
use Tobento\Service\Repository\Storage\Migration\RepositoryDeleteAction;
use Tobento\Service\Repository\Storage\StorageRepository;
use Tobento\Service\Repository\Storage\Column;
use Tobento\Service\Migration\ActionInterface;
use Tobento\Service\Storage\PdoMySqlStorage;
use PDO;
use PDOException;

/**
 * RepositoryActionWithPdoMySqlStorageTest
 */
class RepositoryActionWithPdoMySqlStorageTest extends TestCase
{
    protected null|PdoMySqlStorage $storage = null;
    
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
        
        $this->storage = new PdoMySqlStorage($pdo);
    }
    
    protected function createRepository()
    {
        return new class(
            storage: $this->storage,
            table: 'products',
            columns: [
                Column\Id::new(),
                Column\Text::new('sku'),
            ],
        ) extends StorageRepository {
            //
        };
    }
    
    public function testProcess()
    {
        $repository = $this->createRepository();
        
        $action = new RepositoryAction(
            repository: $repository,
            items: [
                ['sku' => 'foo'],
            ],
        );
        
        $action->process();
        
        $this->assertSame(1, $repository->count());
        
        // delete action:
        (new RepositoryDeleteAction(
            repository: $this->createRepository(),
        ))->process();
        
        $deleted = false;
        
        try {
            $repository->count();
        } catch (PDOException $e) {
            $deleted = true;
        }
        
        $this->assertTrue($deleted);
    }
}