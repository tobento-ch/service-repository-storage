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
use Tobento\Service\Repository\Storage\Column;
use Tobento\Service\Repository\Storage\Test\Helper\Dates;
use Tobento\Service\Dater\DateFormatter;

/**
 * WriteRepositoryColumnWriteMethodTest
 */
abstract class WriteRepositoryColumnWriteMethodTest extends TestCase
{
    protected null|WriteRepositoryInterface $repository = null;

    public function getColumns(): array
    {
        return [
            Column\Id::new(),
            Column\Boolean::new('bool')
                ->write(fn (bool $value, array $attributes): bool => !$value),
            Column\Datetime::new('datetime')
                ->write(fn (mixed $value, array $attributes, DateFormatter $df): string => $df->format(value: $value, format: 'Y-m-d')),
            Column\FloatCol::new('float')
                ->write(fn (float $value, array $attributes): float => $value * -1),
            Column\Integer::new('int')
                ->write(fn (int $value, array $attributes): int => $value * -1),
            Column\Json::new('json')
                ->write(fn (array $value, array $attributes): array => $value),
            Column\Text::new('text')
                ->write(fn (string $value, array $attributes): string => ucfirst($value)),
            Column\Translatable::new('trans')
                ->write(fn (string $value, array $attributes, string $locale): string => ucfirst($value)),
            Column\Translatable::new('trans_array', subtype: 'array')
                ->write(fn (array $value, array $attributes, string $locale): array => ['color' => 'red']),
        ];
    }
    
    public function testCreateMethod()
    {
        $this->repository->locales('en', 'de');
        
        $created = $this->repository->create([
            'bool' => true,
            'datetime' => null,
            'float' => 1.5,
            'int' => 1,
            'json' => null,
            'text' => 'lorem',
            'trans' => ['en' => 'lorem', 'de' => 'ipsum'],
            'trans_array' => ['en' => ['color' => 'blue']],
        ]);
        
        $this->assertFalse($created->get('bool'));
        $this->assertTrue(Dates::isDateFormat('Y-m-d', $created->get('datetime')));
        $this->assertSame(-1.5, $created->get('float'));
        $this->assertSame(-1, $created->get('int'));
        $this->assertIsArray($created->get('json'));
        $this->assertSame('Lorem', $created->get('text'));
        $this->assertSame(['en' => 'Lorem', 'de' => 'Ipsum'], $created->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'red']], $created->get('trans_array')->all());
    }
    
    public function testUpdateByIdMethod()
    {
        $this->repository->locales('en', 'de');
        
        $created = $this->repository->create(['id' => 1]);
        
        $updated = $this->repository->updateById(1, [
            'bool' => true,
            'datetime' => null,
            'float' => 1.5,
            'int' => 1,
            'json' => null,
            'text' => 'lorem',
            'trans' => ['en' => 'lorem', 'de' => 'ipsum'],
            'trans_array' => ['en' => ['color' => 'blue']],
        ]);
        
        $this->assertFalse($updated->get('bool'));
        $this->assertTrue(Dates::isDateFormat('Y-m-d', $updated->get('datetime')));
        $this->assertSame(-1.5, $updated->get('float'));
        $this->assertSame(-1, $updated->get('int'));
        $this->assertIsArray($updated->get('json'));
        $this->assertSame('Lorem', $updated->get('text'));
        $this->assertSame(['en' => 'Lorem', 'de' => 'Ipsum'], $updated->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'red']], $updated->get('trans_array')->all());
    }
    
    public function testUpdateMethod()
    {
        $this->repository->locales('en', 'de');
        
        $created = $this->repository->create(['id' => 1]);
        
        $updated = $this->repository->update(where: [], attributes: [
            'bool' => true,
            'datetime' => null,
            'float' => 1.5,
            'int' => 1,
            'json' => null,
            'text' => 'lorem',
            'trans' => ['en' => 'lorem', 'de' => 'ipsum'],
            'trans_array' => ['en' => ['color' => 'blue']],
        ])->first();
        
        $this->assertFalse($updated->get('bool'));
        $this->assertTrue(Dates::isDateFormat('Y-m-d', $updated->get('datetime')));
        $this->assertSame(-1.5, $updated->get('float'));
        $this->assertSame(-1, $updated->get('int'));
        $this->assertIsArray($updated->get('json'));
        $this->assertSame('Lorem', $updated->get('text'));
        $this->assertSame(['en' => 'Lorem', 'de' => 'Ipsum'], $updated->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'red']], $updated->get('trans_array')->all());
    }
}