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
use Tobento\Service\Repository\Storage\Test\Helper\Dates;
use Tobento\Service\Dater\DateFormatter;

abstract class ReadRepositoryColumnReadMethod extends TestCase
{
    protected null|ReadRepositoryInterface $repository = null;

    public function getColumns(): array
    {
        return [
            new Column\Id(),
            new Column\Boolean('bool')
                ->read(fn (bool $value, array $attributes): bool => !$value),
            new Column\Datetime('datetime')
                ->read(fn (mixed $value, array $attributes, DateFormatter $df): string => $df->format(value: $value, format: 'Y-m-d')),
            new Column\FloatCol('float')
                ->read(fn (float $value, array $attributes): float => $value * -1),
            new Column\Integer('int')
                ->read(fn (int $value, array $attributes): int => $value * -1),
            new Column\Json('json')
                ->read(fn (array $value, array $attributes): array => $value),
            new Column\Text('text')
                ->read(fn (string $value, array $attributes): string => ucfirst($value)),
            new Column\Translatable('trans')
                ->read(fn (string $value, array $attributes, string $locale): string => ucfirst($value)),
            new Column\Translatable('trans_array', subtype: 'array')
                ->read(fn (array $value, array $attributes, string $locale): array => ['color' => 'red']),
        ];
    }
    
    public function testFindByIdMethod()
    {
        $this->repository->storage()->table($this->repository->table())->insert([
            'bool' => true,
            'datetime' => null,
            'float' => 1.5,
            'int' => 1,
            'json' => null,
            'text' => 'lorem',
            'trans' => ['en' => 'lorem', 'de' => 'ipsum'],
            'trans_array' => ['en' => ['color' => 'blue']],
        ]);
        
        $this->repository->locales('en', 'de');
        
        $entity = $this->repository->findById(id: 1);
        
        $this->assertFalse($entity->get('bool'));
        $this->assertTrue(Dates::isDateFormat('Y-m-d', $entity->get('datetime')));
        $this->assertSame(-1.5, $entity->get('float'));
        $this->assertSame(-1, $entity->get('int'));
        $this->assertIsArray($entity->get('json'));
        $this->assertSame('Lorem', $entity->get('text'));
        $this->assertSame(['en' => 'Lorem', 'de' => 'Ipsum'], $entity->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'red']], $entity->get('trans_array')->all());
    }
    
    public function testFindByIdsMethod()
    {
        $this->repository->storage()->table($this->repository->table())->insert([
            'bool' => true,
            'datetime' => null,
            'float' => 1.5,
            'int' => 1,
            'json' => null,
            'text' => 'lorem',
            'trans' => ['en' => 'lorem', 'de' => 'ipsum'],
            'trans_array' => ['en' => ['color' => 'blue']],
        ]);
        
        $this->repository->locales('en', 'de');
        
        $entity = $this->repository->findByIds(1)->first();
        
        $this->assertFalse($entity->get('bool'));
        $this->assertTrue(Dates::isDateFormat('Y-m-d', $entity->get('datetime')));
        $this->assertSame(-1.5, $entity->get('float'));
        $this->assertSame(-1, $entity->get('int'));
        $this->assertIsArray($entity->get('json'));
        $this->assertSame('Lorem', $entity->get('text'));
        $this->assertSame(['en' => 'Lorem', 'de' => 'Ipsum'], $entity->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'red']], $entity->get('trans_array')->all());
    }
    
    public function testFindOneMethod()
    {
        $this->repository->storage()->table($this->repository->table())->insert([
            'bool' => true,
            'datetime' => null,
            'float' => 1.5,
            'int' => 1,
            'json' => null,
            'text' => 'lorem',
            'trans' => ['en' => 'lorem', 'de' => 'ipsum'],
            'trans_array' => ['en' => ['color' => 'blue']],
        ]);
        
        $this->repository->locales('en', 'de');
        
        $entity = $this->repository->findOne();
        
        $this->assertFalse($entity->get('bool'));
        $this->assertTrue(Dates::isDateFormat('Y-m-d', $entity->get('datetime')));
        $this->assertSame(-1.5, $entity->get('float'));
        $this->assertSame(-1, $entity->get('int'));
        $this->assertIsArray($entity->get('json'));
        $this->assertSame('Lorem', $entity->get('text'));
        $this->assertSame(['en' => 'Lorem', 'de' => 'Ipsum'], $entity->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'red']], $entity->get('trans_array')->all());
    }
    
    public function testFindAllMethod()
    {
        $this->repository->storage()->table($this->repository->table())->insert([
            'bool' => true,
            'datetime' => null,
            'float' => 1.5,
            'int' => 1,
            'json' => null,
            'text' => 'lorem',
            'trans' => ['en' => 'lorem', 'de' => 'ipsum'],
            'trans_array' => ['en' => ['color' => 'blue']],
        ]);
        
        $this->repository->locales('en', 'de');
        
        $entity = $this->repository->findAll()->first();
        
        $this->assertFalse($entity->get('bool'));
        $this->assertTrue(Dates::isDateFormat('Y-m-d', $entity->get('datetime')));
        $this->assertSame(-1.5, $entity->get('float'));
        $this->assertSame(-1, $entity->get('int'));
        $this->assertIsArray($entity->get('json'));
        $this->assertSame('Lorem', $entity->get('text'));
        $this->assertSame(['en' => 'Lorem', 'de' => 'Ipsum'], $entity->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'red']], $entity->get('trans_array')->all());
    }    
}