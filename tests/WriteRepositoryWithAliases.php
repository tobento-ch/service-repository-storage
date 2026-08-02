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
use Tobento\Service\Repository\WriteRepositoryInterface;
use Tobento\Service\Repository\ReadRepositoryInterface;
use Tobento\Service\Repository\Storage\Column;
use Tobento\Service\Storage\ItemInterface;

abstract class WriteRepositoryWithAliases extends TestCase
{
    protected null|WriteRepositoryInterface $repository = null;

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
        )->withAliases(aliases: [
            'product_sku'   => 'sku',
            'product_price' => 'price',
            'product_name'  => 'name',
            'product_title' => 'title',
            'product_color' => 'options->color',
        ], readonly: false);
    }

    public function testCreateUsingAlias()
    {
        $this->repository->create([
            'product_sku' => 'pen',
            'product_price' => 1.4,
        ]);

        $entity = $this->repository->findOne(where: ['sku' => 'pen']);

        $this->assertInstanceOf(ItemInterface::class, $entity);
        $this->assertSame('pen', $entity->get('sku'));
        $this->assertSame(1.4, $entity->get('price'));

        // Alias hydration
        $this->assertSame('pen', $entity->get('product_sku'));
        $this->assertSame(1.4, $entity->get('product_price'));
    }

    public function testCreateUsingJsonPathAlias()
    {
        $this->repository->create([
            'product_sku' => 'pen',
            'product_color' => 'blue',
        ]);

        $entity = $this->repository->findOne(where: ['sku' => 'pen']);

        $this->assertSame('blue', $entity->get('product_color'));
        $this->assertSame('blue', $entity->get('options')['color']);
    }

    public function testUpdateUsingAlias()
    {
        $entity = $this->repository->create([
            'sku' => 'pen',
            'price' => 1.0,
        ]);
        
        $this->repository->update(
            where: [],
            attributes: [
                'price' => 2.5,
            ],
        );

        $updated = $this->repository->findOne(where: ['sku' => 'pen']);

        $this->assertSame(2.5, $updated->get('price'));
        $this->assertSame(2.5, $updated->get('product_price'));
    }

    public function testUpdateUsingJsonPathAlias()
    {
        $entity = $this->repository->create([
            'sku' => 'pen',
            'options' => ['color' => 'red'],
        ]);

        $this->repository->update(
            where: [],
            attributes: [
                'product_color' => 'blue',
            ],
        );

        $updated = $this->repository->findOne(where: ['sku' => 'pen']);

        $this->assertSame('blue', $updated->get('product_color'));
        $this->assertSame('blue', $updated->get('options')['color']);
    }

    public function testInvalidAliasIsIgnoredOnWrite()
    {
        $this->repository->create([
            'unknown_alias' => 'foo',
            'sku' => 'pen',
        ]);

        $entity = $this->repository->findOne(where: ['sku' => 'pen']);

        $this->assertSame('pen', $entity->get('sku'));
        $this->assertNull($entity->get('unknown_alias'));
    }
}