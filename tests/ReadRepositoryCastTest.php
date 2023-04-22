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

namespace Tobento\Service\Repository\Storage\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Repository\ReadRepositoryInterface;
use Tobento\Service\Repository\Storage\Column;
use Tobento\Service\Repository\Storage\Attribute\StringTranslations;
use Tobento\Service\Repository\Storage\Attribute\ArrayTranslations;

/**
 * ReadRepositoryCastTest
 */
abstract class ReadRepositoryCastTest extends TestCase
{
    protected null|ReadRepositoryInterface $repository = null;

    public function getColumns(): array
    {
        return [
            Column\Id::new(),
            Column\Boolean::new('bool'),
            Column\Datetime::new('datetime'),
            Column\Datetime::new('date', type: 'date'),
            Column\Datetime::new('time', type: 'time'),
            Column\Datetime::new('timestamp', type: 'timestamp'),
            Column\FloatCol::new('float'),
            Column\Integer::new('int'),
            Column\Json::new('json'),
            Column\Text::new('text'),
            Column\Translatable::new('trans'),
            Column\Translatable::new('trans_array', subtype: 'array'),
        ];
    }
    
    public function testFindByIdMethod()
    {
        // we use the storage to insert the data so no casting is done by writing:
        $this->repository->storage()->table($this->repository->table())->insert([
            'id' => null,
            'bool' => null,
            'datetime' => null,
            'date' => null,
            'time' => null,
            'timestamp' => null,
            'float' => null,
            'int' => null,
            'json' => null,
            'text' => null,
            'trans' => null,
            'trans_array' => null,
        ]);
        
        $entity = $this->repository->findById(id: 1);
        
        $this->assertIsInt($entity->get('id'));
        $this->assertIsBool($entity->get('bool'));
        $this->assertIsString($entity->get('datetime'));
        $this->assertIsString($entity->get('date'));
        $this->assertIsString($entity->get('time'));
        $this->assertIsString($entity->get('timestamp'));
        $this->assertIsFloat($entity->get('float'));
        $this->assertIsInt($entity->get('int'));
        $this->assertIsArray($entity->get('json'));
        $this->assertIsString($entity->get('text'));
        $this->assertTrue($entity->get('trans') instanceof StringTranslations);
        $this->assertTrue($entity->get('trans_array') instanceof ArrayTranslations);
    }
    
    public function testFindByIdsMethod()
    {
        // we use the storage to insert the data so no casting is done by writing:
        $this->repository->storage()->table($this->repository->table())->insert([
            'id' => null,
            'bool' => null,
            'datetime' => null,
            'date' => null,
            'time' => null,
            'timestamp' => null,
            'float' => null,
            'int' => null,
            'json' => null,
            'text' => null,
            'trans' => null,
            'trans_array' => null,
        ]);
        
        $entity = $this->repository->findByIds(1)->first();
        
        $this->assertIsInt($entity->get('id'));
        $this->assertIsBool($entity->get('bool'));
        $this->assertIsString($entity->get('datetime'));
        $this->assertIsString($entity->get('date'));
        $this->assertIsString($entity->get('time'));
        $this->assertIsString($entity->get('timestamp'));
        $this->assertIsFloat($entity->get('float'));
        $this->assertIsInt($entity->get('int'));
        $this->assertIsArray($entity->get('json'));
        $this->assertIsString($entity->get('text'));
        $this->assertTrue($entity->get('trans') instanceof StringTranslations);
        $this->assertTrue($entity->get('trans_array') instanceof ArrayTranslations);
    }
    
    public function testFindOneMethod()
    {
        // we use the storage to insert the data so no casting is done by writing:
        $this->repository->storage()->table($this->repository->table())->insert([
            'id' => null,
            'bool' => null,
            'datetime' => null,
            'date' => null,
            'time' => null,
            'timestamp' => null,
            'float' => null,
            'int' => null,
            'json' => null,
            'text' => null,
            'trans' => null,
            'trans_array' => null,
        ]);
        
        $entity = $this->repository->findOne();
        
        $this->assertIsInt($entity->get('id'));
        $this->assertIsBool($entity->get('bool'));
        $this->assertIsString($entity->get('datetime'));
        $this->assertIsString($entity->get('date'));
        $this->assertIsString($entity->get('time'));
        $this->assertIsString($entity->get('timestamp'));
        $this->assertIsFloat($entity->get('float'));
        $this->assertIsInt($entity->get('int'));
        $this->assertIsArray($entity->get('json'));
        $this->assertIsString($entity->get('text'));
        $this->assertTrue($entity->get('trans') instanceof StringTranslations);
        $this->assertTrue($entity->get('trans_array') instanceof ArrayTranslations);
    }
    
    public function testFindAllMethod()
    {
        // we use the storage to insert the data so no casting is done by writing:
        $this->repository->storage()->table($this->repository->table())->insert([
            'id' => null,
            'bool' => null,
            'datetime' => null,
            'date' => null,
            'time' => null,
            'timestamp' => null,
            'float' => null,
            'int' => null,
            'json' => null,
            'text' => null,
            'trans' => null,
            'trans_array' => null,
        ]);
        
        $entity = $this->repository->findAll()->first();
        
        $this->assertIsInt($entity->get('id'));
        $this->assertIsBool($entity->get('bool'));
        $this->assertIsString($entity->get('datetime'));
        $this->assertIsString($entity->get('date'));
        $this->assertIsString($entity->get('time'));
        $this->assertIsString($entity->get('timestamp'));
        $this->assertIsFloat($entity->get('float'));
        $this->assertIsInt($entity->get('int'));
        $this->assertIsArray($entity->get('json'));
        $this->assertIsString($entity->get('text'));
        $this->assertTrue($entity->get('trans') instanceof StringTranslations);
        $this->assertTrue($entity->get('trans_array') instanceof ArrayTranslations);
    }    
}