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
use Tobento\Service\Repository\WriteRepositoryInterface;
use Tobento\Service\Repository\Storage\Column;
use Tobento\Service\Storage\ItemInterface;
use Tobento\Service\Storage\ItemsInterface;

/**
 * ReadRepositoryTest
 */
abstract class ReadRepositoryTest extends TestCase
{
    protected null|ReadRepositoryInterface $repository = null;
    
    protected null|WriteRepositoryInterface $writeRepository = null;

    public function getColumns(): array
    {
        return [
            Column\Id::new(),
            Column\Boolean::new('active'),
            Column\Datetime::new('created'),
            Column\FloatCol::new('price'),
            Column\Integer::new('count'),
            Column\Json::new('options'),
            Column\Text::new('sku'),
            Column\Translatable::new('title'),
        ];
    }
    
    public function testFindByIdMethod()
    {
        $this->writeRepository->create(['sku' => 'scissors']);
        
        $entity = $this->repository->findById(id: 1);
        
        $this->assertInstanceof(ItemInterface::class, $entity);
        $this->assertSame(1, $entity->get('id'));
    }
    
    public function testFindByIdMethodReturnsNullIfNotFound()
    {
        $entity = $this->repository->findById(id: 1);

        $this->assertSame(null, $entity);
    }
    
    public function testFindByIdsMethod()
    {
        $this->assertSame(0, $this->repository->findByIds(1, 2, 8)->count());
        
        $this->writeRepository->create(['sku' => 'scissors']);
        $this->writeRepository->create(['sku' => 'pen']);
        
        $entities = $this->repository->findByIds(1, 2, 8);
        
        $this->assertInstanceof(ItemsInterface::class, $entities);
        $this->assertSame(2, $entities->count());
    }
    
    public function testFindOneMethod()
    {
        $this->writeRepository->create(['sku' => 'scissors']);
        $this->writeRepository->create(['sku' => 'pen']);
        
        $entity = $this->repository->findOne();
        
        $this->assertInstanceof(ItemInterface::class, $entity);
        $this->assertSame('scissors', $entity->get('sku'));
    }
    
    public function testFindOneMethodWhereParameters()
    {
        $this->writeRepository->create([
            'sku' => 'scissors', 'price' => 1.2, 'options' => ['colors' => ['blue']], 'created' => '2023-11-25 10:05',
        ]);
        $this->writeRepository->create([
            'sku' => 'pen', 'price' => 1.4, 'options' => ['color' => 'red', 'colors' => ['blue', 'red']],
        ]);
        $this->writeRepository->create([
            'sku' => 'pencil', 'price' => 0.8, 'options' => ['color' => 'yellow', 'colors' => ['yellow']],
        ]);
        
        $this->assertSame('pen', $this->repository->findOne(where: ['sku' => 'pen'])?->get('sku'));
        $this->assertSame('pen', $this->repository->findOne(where: ['sku' => ['=' => 'pen']])?->get('sku'));
        $this->assertSame('scissors', $this->repository->findOne(where: ['sku' => ['!=' => 'pen']])?->get('sku'));
        $this->assertSame('pen', $this->repository->findOne(where: ['created' => ['null']])?->get('sku'));
        $this->assertSame('scissors', $this->repository->findOne(where: ['created' => ['not null' => '']])?->get('sku'));
        $this->assertSame('pen', $this->repository->findOne(where: ['price' => ['>' => 1.3]])?->get('sku'));
        $this->assertSame('pencil', $this->repository->findOne(where: ['price' => ['<' => 1.2]])?->get('sku'));
        $this->assertSame('scissors', $this->repository->findOne(where: ['price' => ['>=' => 1.2]])?->get('sku'));
        $this->assertSame('scissors', $this->repository->findOne(where: ['price' => ['<=' => 1.2]])?->get('sku'));
        $this->assertSame('pen', $this->repository->findOne(where: ['price' => ['<>' => 1.2]])?->get('sku'));
        $this->assertSame('scissors', $this->repository->findOne(where: ['price' => ['<=>' => 1.2]])?->get('sku'));
        $this->assertSame('pencil', $this->repository->findOne(where: ['price' => ['between' => [0.5, 0.9]]])?->get('sku'));
        $this->assertSame('scissors', $this->repository->findOne(where: ['price' => ['not between' => [0.5, 0.9]]])?->get('sku'));
        $this->assertSame('pen', $this->repository->findOne(where: ['id' => ['in' => [2,3]]])?->get('sku'));
        $this->assertSame('scissors', $this->repository->findOne(where: ['id' => ['not in' => [2,3]]])?->get('sku'));
        $this->assertSame('pen', $this->repository->findOne(where: ['sku' => ['like' => 'p%']])?->get('sku'));
        $this->assertSame('scissors', $this->repository->findOne(where: ['sku' => ['not like' => 'p%']])?->get('sku'));
        $this->assertSame('pen', $this->repository->findOne(where: ['sku' => ['like' => '%n']])?->get('sku'));
        $this->assertSame('scissors', $this->repository->findOne(where: ['sku' => ['not like' => '%n']])?->get('sku'));
        $this->assertSame('pen', $this->repository->findOne(where: ['sku' => ['like' => '%en%']])?->get('sku'));
        $this->assertSame('scissors', $this->repository->findOne(where: ['sku' => ['not like' => '%en%']])?->get('sku'));
        $this->assertSame('pen', $this->repository->findOne(where: ['options->color' => 'red'])?->get('sku'));
        $this->assertSame('pencil', $this->repository->findOne(where: ['options->colors' => ['contains' => 'yellow']])?->get('sku'));
        $this->assertSame('pen', $this->repository->findOne(where: ['options->colors' => ['contains' => ['red']]])?->get('sku'));
        $this->assertSame('pen', $this->repository->findOne(where: ['options->color' => ['contains key']])?->get('sku'));
        
        // invalid tests.
        $this->assertSame(null, $this->repository->findOne(where: ['sku' => ['invalid' => 'foo']]));
        $this->assertSame('scissors', $this->repository->findOne(where: [[]])?->get('sku'));
        $this->assertSame(null, $this->repository->findOne(where: ['sku' => [[]]])?->get('sku'));
        $this->assertSame(null, $this->repository->findOne(where: ['unknown' => 'foo']));
        $this->assertSame('scissors', $this->repository->findOne(where: ['price' => ['between' => 'invalid']])?->get('sku'));
        $this->assertSame('scissors', $this->repository->findOne(where: ['price' => ['not between' => 'invalid']])?->get('sku'));
        $this->assertSame(null, $this->repository->findOne(where: ['price' => ['in' => 'invalid']])?->get('sku'));
        $this->assertSame(null, $this->repository->findOne(where: ['price' => ['not in' => 'invalid']])?->get('sku'));
        $this->assertSame(null, $this->repository->findOne(where: ['sku' => ['like' => '']])?->get('sku'));
        $this->assertSame('scissors', $this->repository->findOne(where: ['options->color' => [[]]])?->get('sku'));
    }
    
    public function testFindOneMethodReturnsNullIfNotFound()
    {
        $entity = $this->repository->findOne(where: ['sku' => 'pen']);
        
        $this->assertSame(null, $entity);
    }
    
    public function testFindAllMethod()
    {
        $this->assertSame(0, $this->repository->findAll()->count());
        
        $this->writeRepository->create(['sku' => 'scissors', 'price' => 1.2]);
        $this->writeRepository->create(['sku' => 'pen', 'price' => 1.4]);
        
        $entities = $this->repository->findAll();
        
        $this->assertInstanceof(ItemsInterface::class, $entities);
        $this->assertSame(2, $entities->count());
    }
    
    public function testFindAllMethodWhereParameters()
    {
        $this->writeRepository->create([
            'sku' => 'scissors', 'price' => 1.2, 'options' => ['colors' => ['blue']], 'created' => '2023-11-25 10:05',
        ]);
        $this->writeRepository->create([
            'sku' => 'pen', 'price' => 1.4, 'options' => ['color' => 'red', 'colors' => ['blue', 'red']],
        ]);
        $this->writeRepository->create([
            'sku' => 'pencil', 'price' => 0.8, 'options' => ['color' => 'yellow', 'colors' => ['yellow']],
        ]);
        
        $this->assertSame('pen', $this->repository->findAll(where: ['sku' => 'pen'])->first()?->get('sku'));
        $this->assertSame('pen', $this->repository->findAll(where: ['sku' => ['=' => 'pen']])->first()?->get('sku'));
        $this->assertSame('scissors', $this->repository->findAll(where: ['sku' => ['!=' => 'pen']])->first()?->get('sku'));
        $this->assertSame('pen', $this->repository->findAll(where: ['created' => ['null']])->first()?->get('sku'));
        $this->assertSame('scissors', $this->repository->findAll(where: ['created' => ['not null' => '']])->first()?->get('sku'));
        $this->assertSame('pen', $this->repository->findAll(where: ['price' => ['>' => 1.3]])->first()?->get('sku'));
        $this->assertSame('pencil', $this->repository->findAll(where: ['price' => ['<' => 1.2]])->first()?->get('sku'));
        $this->assertSame('scissors', $this->repository->findAll(where: ['price' => ['>=' => 1.2]])->first()?->get('sku'));
        $this->assertSame('scissors', $this->repository->findAll(where: ['price' => ['<=' => 1.2]])->first()?->get('sku'));
        $this->assertSame('pen', $this->repository->findAll(where: ['price' => ['<>' => 1.2]])->first()?->get('sku'));
        $this->assertSame('scissors', $this->repository->findAll(where: ['price' => ['<=>' => 1.2]])->first()?->get('sku'));
        $this->assertSame('pencil', $this->repository->findAll(where: ['price' => ['between' => [0.5, 0.9]]])->first()?->get('sku'));
        $this->assertSame('scissors', $this->repository->findAll(where: ['price' => ['not between' => [0.5, 0.9]]])->first()?->get('sku'));
        $this->assertSame('pen', $this->repository->findAll(where: ['id' => ['in' => [2,3]]])->first()?->get('sku'));
        $this->assertSame('scissors', $this->repository->findAll(where: ['id' => ['not in' => [2,3]]])->first()?->get('sku'));
        $this->assertSame('pen', $this->repository->findAll(where: ['sku' => ['like' => 'p%']])->first()?->get('sku'));
        $this->assertSame('scissors', $this->repository->findAll(where: ['sku' => ['not like' => 'p%']])->first()?->get('sku'));
        $this->assertSame('pen', $this->repository->findAll(where: ['sku' => ['like' => '%n']])->first()?->get('sku'));
        $this->assertSame('scissors', $this->repository->findAll(where: ['sku' => ['not like' => '%n']])->first()?->get('sku'));
        $this->assertSame('pen', $this->repository->findAll(where: ['sku' => ['like' => '%en%']])->first()?->get('sku'));
        $this->assertSame('scissors', $this->repository->findAll(where: ['sku' => ['not like' => '%en%']])->first()?->get('sku'));
        $this->assertSame('pen', $this->repository->findAll(where: ['options->color' => 'red'])->first()?->get('sku'));
        $this->assertSame('pencil', $this->repository->findAll(where: ['options->colors' => ['contains' => 'yellow']])->first()?->get('sku'));
        $this->assertSame('pen', $this->repository->findAll(where: ['options->colors' => ['contains' => ['red']]])->first()?->get('sku'));
        $this->assertSame('pen', $this->repository->findAll(where: ['options->color' => ['contains key']])->first()?->get('sku'));
        
        // invalid tests.
        $this->assertSame(0, $this->repository->findAll(where: ['sku' => ['invalid' => 'foo']])->count());
        $this->assertSame('scissors', $this->repository->findAll(where: [[]])->first()?->get('sku'));
        $this->assertSame(null, $this->repository->findAll(where: ['sku' => [[]]])->first()?->get('sku'));
        $this->assertSame(0, $this->repository->findAll(where: ['unknown' => 'foo'])->count());
        $this->assertSame('scissors', $this->repository->findAll(where: ['price' => ['between' => 'invalid']])->first()?->get('sku'));
        $this->assertSame('scissors', $this->repository->findAll(where: ['price' => ['not between' => 'invalid']])->first()?->get('sku'));
        $this->assertSame(null, $this->repository->findAll(where: ['price' => ['in' => 'invalid']])->first()?->get('sku'));
        $this->assertSame(null, $this->repository->findAll(where: ['price' => ['not in' => 'invalid']])->first()?->get('sku'));
        $this->assertSame(0, $this->repository->findAll(where: ['sku' => ['like' => '']])->count());
        $this->assertSame('scissors', $this->repository->findAll(where: ['options->color' => [[]]])->first()?->get('sku'));
    }
    
    public function testFindAllMethodOrderByParameter()
    {
        $this->writeRepository->create(['sku' => 'b']);
        $this->writeRepository->create(['sku' => 'a']);
        $this->writeRepository->create(['sku' => 'c']);
        $this->writeRepository->create(['sku' => 'e']);
        $this->writeRepository->create(['sku' => 'd']);
        
        $entities = $this->repository->findAll(orderBy: ['sku' => 'asc']);
        $this->assertSame('a', $entities->first()->get('sku'));
        
        $entities = $this->repository->findAll(orderBy: ['sku' => 'desc']);
        $this->assertSame('e', $entities->first()->get('sku'));
        
        $entities = $this->repository->findAll(orderBy: ['sku' => []]);
        $this->assertSame('b', $entities->first()->get('sku'));
        
        $entities = $this->repository->findAll(orderBy: ['unknown' => 'asc']);
        $this->assertSame('b', $entities->first()->get('sku'));
    }
    
    public function testFindAllMethodLimitParameter()
    {
        $this->writeRepository->create(['sku' => 'a']);
        $this->writeRepository->create(['sku' => 'b']);
        $this->writeRepository->create(['sku' => 'c']);
        $this->writeRepository->create(['sku' => 'd']);
        $this->writeRepository->create(['sku' => 'e']);
        
        $entities = $this->repository->findAll(limit: 2);
        $this->assertSame(2, $entities->count());
        
        $entities = $this->repository->findAll(limit: [5, 3]);
        $this->assertSame(2, $entities->count());
        $this->assertSame('d', $entities->first()->get('sku'));
        
        $entities = $this->repository->findAll(limit: []);
        $this->assertSame(5, $entities->count());
        
        $entities = $this->repository->findAll(limit: ['bar', 'foo']);
        $this->assertSame(5, $entities->count());
    }
    
    public function testFindAllMethodIsIndexedByPrimaryKey()
    {
        $this->assertSame(0, $this->repository->findAll()->count());
        
        $this->writeRepository->create(['sku' => 'scissors', 'price' => 1.2]);
        $this->writeRepository->create(['sku' => 'pen', 'price' => 1.4]);
        
        $entities = $this->repository->findAll();
        
        $this->assertSame([0 => 1, 1 => 2], array_keys($entities->all()));
    }    
}