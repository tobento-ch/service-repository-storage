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
use Tobento\Service\Repository\Storage\Test\Mock\ProductRepository;
use Tobento\Service\Storage\JsonFileStorage;
use Tobento\Service\Filesystem\Dir;

/**
 * RepositoryReadTest
 */
class RepositoryReadTest extends \Tobento\Service\Repository\Storage\Test\ReadRepositoryTest
{
    public function setUp(): void
    {
        $this->repository = new ProductRepository(
            storage: new JsonFileStorage(dir: __DIR__.'/tmp/'),
            table: 'products',
            columns: $this->getColumns(),
        );
        
        $this->writeRepository = $this->repository;
        
        (new Dir())->delete(__DIR__.'/tmp/');
    }

    public function tearDown(): void
    {
        (new Dir())->delete(__DIR__.'/tmp/');
    }
}