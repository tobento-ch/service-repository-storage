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

/**
 * ReadRepositoryTranslationsTest
 */
abstract class ReadRepositoryTranslationsTest extends TestCase
{
    protected null|ReadRepositoryInterface $repository = null;

    public function getColumns(): array
    {
        return [
            Column\Id::new(),
            Column\Translatable::new('trans'),
            Column\Translatable::new('trans_array', subtype: 'array'),
        ];
    }
    
    public function testFindByIdMethod()
    {
        $this->repository->storage()->table($this->repository->table())->insert([
            'trans' => ['en' => 'En', 'de' => 'De', 'fr' => 'Fr'],
            'trans_array' => ['en' => ['a' => 'A'], 'de' => ['b' => 'B'], 'fr' => ['c' => 'C']],
        ]);
        
        $this->repository->locale('de');
        $this->repository->locales('en', 'de');
        $this->repository->localeFallbacks(['fr' => 'de']);
        
        $entity = $this->repository->findById(id: 1);

        $this->assertSame(['en' => 'En', 'de' => 'De'], $entity->get('trans')->all());
        $this->assertSame('De', $entity->get('trans')->get());
        $this->assertSame('De', $entity->get('trans')->get(locale: 'fr'));
        $this->assertSame(['en' => ['a' => 'A'], 'de' => ['b' => 'B']], $entity->get('trans_array')->all());
        $this->assertSame(['b' => 'B'], $entity->get('trans_array')->get());
        $this->assertSame(['b' => 'B'], $entity->get('trans_array')->get(locale: 'fr'));
    }
    
    public function testFindByIdsMethod()
    {
        $this->repository->storage()->table($this->repository->table())->insert([
            'trans' => ['en' => 'En', 'de' => 'De', 'fr' => 'Fr'],
            'trans_array' => ['en' => ['a' => 'A'], 'de' => ['b' => 'B'], 'fr' => ['c' => 'C']],
        ]);
        
        $this->repository->locale('de');
        $this->repository->locales('en', 'de');
        $this->repository->localeFallbacks(['fr' => 'de']);
        
        $entity = $this->repository->findByIds(1)->first();

        $this->assertSame(['en' => 'En', 'de' => 'De'], $entity->get('trans')->all());
        $this->assertSame('De', $entity->get('trans')->get());
        $this->assertSame('De', $entity->get('trans')->get(locale: 'fr'));
        $this->assertSame(['en' => ['a' => 'A'], 'de' => ['b' => 'B']], $entity->get('trans_array')->all());
        $this->assertSame(['b' => 'B'], $entity->get('trans_array')->get());
        $this->assertSame(['b' => 'B'], $entity->get('trans_array')->get(locale: 'fr'));
    }
    
    public function testFindOneMethod()
    {
        $this->repository->storage()->table($this->repository->table())->insert([
            'trans' => ['en' => 'En', 'de' => 'De', 'fr' => 'Fr'],
            'trans_array' => ['en' => ['a' => 'A'], 'de' => ['b' => 'B'], 'fr' => ['c' => 'C']],
        ]);
        
        $this->repository->locale('de');
        $this->repository->locales('en', 'de');
        $this->repository->localeFallbacks(['fr' => 'de']);
        
        $entity = $this->repository->findOne();

        $this->assertSame(['en' => 'En', 'de' => 'De'], $entity->get('trans')->all());
        $this->assertSame('De', $entity->get('trans')->get());
        $this->assertSame('De', $entity->get('trans')->get(locale: 'fr'));
        $this->assertSame(['en' => ['a' => 'A'], 'de' => ['b' => 'B']], $entity->get('trans_array')->all());
        $this->assertSame(['b' => 'B'], $entity->get('trans_array')->get());
        $this->assertSame(['b' => 'B'], $entity->get('trans_array')->get(locale: 'fr'));
    }
    
    public function testFindOneMethodWhereCurrentLocale()
    {
        $this->repository->storage()->table($this->repository->table())->insert([
            'trans' => ['en' => 'En', 'de' => 'De'],
            'trans_array' => ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
        ]);
        
        $this->repository->locale('de');
        $this->repository->locales('en', 'de');
        
        $entity = $this->repository->findOne(where: ['trans' => 'De']);
        $this->assertSame(1, $entity->get('id'));
        
        $entity = $this->repository->findOne(where: ['trans_array->color' => 'rot']);
        $this->assertSame(1, $entity->get('id'));
    }
    
    public function testFindOneMethodWhereSepcificLocale()
    {
        $this->repository->storage()->table($this->repository->table())->insert([
            'trans' => ['en' => 'En', 'de' => 'De'],
            'trans_array' => ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
        ]);
        
        $this->repository->locale('de');
        $this->repository->locales('en', 'de');
        
        $entity = $this->repository->findOne(where: ['trans->en' => 'En']);
        $this->assertSame(1, $entity->get('id'));
        
        $entity = $this->repository->findOne(where: ['trans_array->en->color' => 'red']);
        $this->assertSame(1, $entity->get('id'));
    }
    
    public function testFindAllMethod()
    {
        $this->repository->storage()->table($this->repository->table())->insert([
            'trans' => ['en' => 'En', 'de' => 'De', 'fr' => 'Fr'],
            'trans_array' => ['en' => ['a' => 'A'], 'de' => ['b' => 'B'], 'fr' => ['c' => 'C']],
        ]);
        
        $this->repository->locale('de');
        $this->repository->locales('en', 'de');
        $this->repository->localeFallbacks(['fr' => 'de']);
        
        $entity = $this->repository->findAll()->first();

        $this->assertSame(['en' => 'En', 'de' => 'De'], $entity->get('trans')->all());
        $this->assertSame('De', $entity->get('trans')->get());
        $this->assertSame('De', $entity->get('trans')->get(locale: 'fr'));
        $this->assertSame(['en' => ['a' => 'A'], 'de' => ['b' => 'B']], $entity->get('trans_array')->all());
        $this->assertSame(['b' => 'B'], $entity->get('trans_array')->get());
        $this->assertSame(['b' => 'B'], $entity->get('trans_array')->get(locale: 'fr'));
    }
    
    public function testFindAllMethodWhereCurrentLocale()
    {
        $this->repository->storage()->table($this->repository->table())->insert([
            'trans' => ['en' => 'En', 'de' => 'De'],
            'trans_array' => ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
        ]);
        
        $this->repository->locale('de');
        $this->repository->locales('en', 'de');
        
        $entity = $this->repository->findAll(where: ['trans' => 'De'])->first();
        $this->assertSame(1, $entity->get('id'));
        
        $entity = $this->repository->findAll(where: ['trans_array->color' => 'rot'])->first();
        $this->assertSame(1, $entity->get('id'));
    }
    
    public function testFindAllMethodWhereSepcificLocale()
    {
        $this->repository->storage()->table($this->repository->table())->insert([
            'trans' => ['en' => 'En', 'de' => 'De'],
            'trans_array' => ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
        ]);
        
        $this->repository->locale('de');
        $this->repository->locales('en', 'de');
        
        $entity = $this->repository->findAll(where: ['trans->en' => 'En'])->first();
        $this->assertSame(1, $entity->get('id'));
        
        $entity = $this->repository->findAll(where: ['trans_array->en->color' => 'red'])->first();
        $this->assertSame(1, $entity->get('id'));
    }    
}