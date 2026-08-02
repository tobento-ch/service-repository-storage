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

abstract class ReadRepositoryWithAliases extends TestCase
{
    protected null|ReadRepositoryInterface $repository = null;
    protected null|WriteRepositoryInterface $writeRepository = null;

    /**
     * Columns with alias support.
     */
    public function getColumns(): Column\ColumnsInterface
    {
        return new Column\AliasedColumns(
            new Column\Id(),
            new Column\Boolean('active'),
            new Column\Datetime('created'),
            new Column\FloatCol('price'),
            new Column\Integer('count'),
            new Column\Json('options'),
            new Column\Text('sku'),
            new Column\Translatable('title'),
            new Column\Text('name'),
        )->withAliases([
            'product_sku'   => 'sku',
            'product_price' => 'price',
            'product_name'  => 'name',
            'product_title' => 'title',
            'product_color' => 'options->color',
        ]);
    }
    
    public function testFindOneIsHydrated()
    {
        $this->writeRepository->create(['sku' => 'scissors', 'price' => 1.2]);
        $this->writeRepository->create(['sku' => 'pen', 'price' => 1.4]);

        $entity = $this->repository->findOne(where: ['product_sku' => 'pen']);

        $this->assertInstanceOf(ItemInterface::class, $entity);

        // Alias must return the value
        $this->assertSame('pen', $entity->get('product_sku'));

        // Raw column must also return the value
        $this->assertSame('pen', $entity->get('sku'));
    }

    public function testFindOneUsingAlias()
    {
        $this->writeRepository->create(['sku' => 'scissors', 'price' => 1.2]);
        $this->writeRepository->create(['sku' => 'pen', 'price' => 1.4]);

        $entity = $this->repository->findOne(where: ['product_sku' => 'pen']);

        $this->assertInstanceOf(ItemInterface::class, $entity);
        $this->assertSame('pen', $entity->get('sku'));
    }

    public function testFindOneUsingAliasOperators()
    {
        $this->writeRepository->create(['sku' => 'scissors', 'price' => 1.2]);
        $this->writeRepository->create(['sku' => 'pen', 'price' => 1.4]);

        $this->assertSame('pen', $this->repository->findOne(where: [
            'product_price' => ['>' => 1.3]
        ])?->get('sku'));

        $this->assertSame('scissors', $this->repository->findOne(where: [
            'product_price' => ['<=' => 1.2]
        ])?->get('sku'));
    }

    public function testFindOneUsingAliasJsonPath()
    {
        $this->writeRepository->create([
            'sku' => 'pen',
            'options' => ['color' => 'red'],
        ]);

        $entity = $this->repository->findOne(where: [
            'product_color' => 'red'
        ]);

        $this->assertSame('pen', $entity?->get('sku'));
    }

    public function testFindAllIsHydrated()
    {
        $this->writeRepository->create([
            'sku' => 'scissors',
            'price' => 1.2,
            'name' => 'Scissors',
            'title' => 'Scissors Title',
            'options' => ['color' => 'blue'],
        ]);

        $this->writeRepository->create([
            'sku' => 'pen',
            'price' => 1.4,
            'name' => 'Pen',
            'title' => 'Pen Title',
            'options' => ['color' => 'red'],
        ]);

        $entities = $this->repository->findAll();

        $this->assertCount(2, $entities);

        // First entity: scissors
        $first = $entities[1];
        $this->assertSame('scissors', $first->get('product_sku'));
        $this->assertSame('scissors', $first->get('sku'));
        $this->assertSame(1.2, $first->get('product_price'));
        $this->assertSame(1.2, $first->get('price'));
        $this->assertSame('Scissors', $first->get('product_name'));
        $this->assertSame('Scissors', $first->get('name'));
        $this->assertSame('Scissors Title', $first->get('product_title')->get());
        $this->assertSame('Scissors Title', $first->get('title')->get());
        $this->assertSame('blue', $first->get('product_color'));

        // Second entity: pen
        $second = $entities[2];
        $this->assertSame('pen', $second->get('product_sku'));
        $this->assertSame('pen', $second->get('sku'));
        $this->assertSame(1.4, $second->get('product_price'));
        $this->assertSame(1.4, $second->get('price'));
        $this->assertSame('Pen', $second->get('product_name'));
        $this->assertSame('Pen', $second->get('name'));
        $this->assertSame('Pen Title', $second->get('product_title')->get());
        $this->assertSame('Pen Title', $second->get('title')->get());
        $this->assertSame('red', $second->get('product_color'));
    }

    public function testFindAllUsingAlias()
    {
        $this->writeRepository->create(['sku' => 'a', 'price' => 1.0]);
        $this->writeRepository->create(['sku' => 'b', 'price' => 2.0]);

        $entities = $this->repository->findAll(where: [
            'product_price' => ['>' => 1.5]
        ]);

        $this->assertSame(1, $entities->count());
        $this->assertSame('b', $entities->first()?->get('sku'));
    }

    public function testFindAllUsingAliasOrClauses()
    {
        $this->writeRepository->create(['sku' => 'scissors', 'price' => 1.2]);
        $this->writeRepository->create(['sku' => 'pen', 'price' => 1.4]);
        $this->writeRepository->create(['sku' => 'pencil', 'price' => 0.8]);

        $entities = $this->repository->findAll(where: [
            'product_sku' => ['like' => '%il%', 'or like' => '%ss%']
        ]);

        $this->assertSame(2, $entities->count());
    }

    public function testOrderByAlias()
    {
        $this->writeRepository->create(['sku' => 'b']);
        $this->writeRepository->create(['sku' => 'a']);
        $this->writeRepository->create(['sku' => 'c']);

        $entities = $this->repository->findAll(orderBy: ['product_sku' => 'asc']);
        $this->assertSame('a', $entities->first()->get('sku'));

        $entities = $this->repository->findAll(orderBy: ['product_sku' => 'desc']);
        $this->assertSame('c', $entities->first()->get('sku'));
    }

    public function testFindColumnAlias()
    {
        $this->writeRepository->create(['sku' => 'scissors']);
        $this->writeRepository->create(['sku' => 'pen']);

        $values = $this->repository->findColumn('product_sku');

        $this->assertSame(['scissors', 'pen'], $values);
    }

    public function testFindColumnAliasWithKey()
    {
        $this->writeRepository->create(['sku' => 'scissors', 'name' => 'Scissors']);
        $this->writeRepository->create(['sku' => 'pen', 'name' => 'Pen']);

        $values = $this->repository->findColumn(column: 'product_sku', key: 'product_name');

        $this->assertSame(['Scissors' => 'scissors', 'Pen' => 'pen'], $values);
    }

    public function testInvalidAlias()
    {
        $this->writeRepository->create(['sku' => 'scissors']);

        $entity = $this->repository->findOne(where: ['unknown_alias' => 'foo']);

        $this->assertSame(null, $entity);
    }
}