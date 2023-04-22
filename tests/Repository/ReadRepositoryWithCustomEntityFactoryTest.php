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

namespace Tobento\Service\Repository\Storage\Test\Repository;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Repository\Storage\Test\Mock\ProductWriteRepository;
use Tobento\Service\Repository\Storage\Test\Mock\ProductReadRepository;
use Tobento\Service\Repository\Storage\Test\Mock\ProductFactory;
use Tobento\Service\Storage\JsonFileStorage;
use Tobento\Service\Filesystem\Dir;

/**
 * ReadRepositoryWithCustomEntityFactoryTest
 */
class ReadRepositoryWithCustomEntityFactoryTest extends \Tobento\Service\Repository\Storage\Test\ReadRepositoryWithCustomEntityFactoryTest
{
    public function setUp(): void
    {
        $this->repository = new ProductReadRepository(
            storage: new JsonFileStorage(dir: __DIR__.'/tmp/'),
            table: 'products',
            columns: $this->getColumns(),
            entityFactory: new ProductFactory(),
        );
        
        $this->writeRepository = new ProductWriteRepository(
            storage: new JsonFileStorage(dir: __DIR__.'/tmp/'),
            table: 'products',
            columns: $this->getColumns(),
            entityFactory: new ProductFactory(),
        );
        
        (new Dir())->delete(__DIR__.'/tmp/');
    }

    public function tearDown(): void
    {
        (new Dir())->delete(__DIR__.'/tmp/');
    }
}