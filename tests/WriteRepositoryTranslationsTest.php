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
use Tobento\Service\Repository\Storage\Test\Helper\Dates;
use Tobento\Service\Repository\WriteRepositoryInterface;
use Tobento\Service\Repository\Storage\Column;
use Tobento\Service\Repository\Storage\Attribute\StringTranslations;
use Tobento\Service\Repository\Storage\Attribute\ArrayTranslations;
use Tobento\Service\Dater\DateFormatter;

/**
 * WriteRepositoryTranslationsTest
 */
abstract class WriteRepositoryTranslationsTest extends TestCase
{
    protected null|WriteRepositoryInterface $repository = null;

    public function getColumns(): array
    {
        return [
            Column\Id::new(),
            Column\Translatable::new('trans'),
            Column\Translatable::new('trans_array', subtype: 'array'),
        ];
    }
    
    public function testCreateMethodOnlyUsesConfiguredLocales()
    {
        $this->repository->locales('en', 'de');
        
        $created = $this->repository->create([
            'trans' => ['en' => 'En', 'de' => 'De', 'fr' => 'Fr'],
            'trans_array' => ['en' => ['color' => 'red'], 'de' => ['color' => 'rot'], 'fr' => ['color' => 'rouge']],
        ]);
        
        $this->assertSame(['en' => 'En', 'de' => 'De'], $created->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'red'], 'de' => ['color' => 'rot']], $created->get('trans_array')->all());
    }
    
    public function testCreateMethodOnlyUsesDefaultConfiguredLocales()
    {
        $created = $this->repository->create([
            'trans' => ['en' => 'En', 'de' => 'De', 'fr' => 'Fr'],
            'trans_array' => ['en' => ['color' => 'red'], 'de' => ['color' => 'rot'], 'fr' => ['color' => 'rouge']],
        ]);
        
        $this->assertSame(['en' => 'En'], $created->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'red']], $created->get('trans_array')->all());
    }
    
    public function testUpdateByIdMethodOnlyUsesConfiguredLocales()
    {
        $this->repository->locale('de');
        $this->repository->locales('en', 'de');
        
        $created = $this->repository->create([
            'trans' => ['en' => 'En', 'de' => 'De'],
            'trans_array' => ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
        ]);
        
        $updated = $this->repository->updateById(1, [
            'trans' => ['en' => 'En New', 'de' => 'De New', 'fr' => 'Fr New'],
            'trans_array' => ['en' => ['color' => 'blue'], 'de' => ['color' => 'blau'], 'fr' => ['color' => 'bleu']],
        ]);
        
        $this->assertSame(['en' => 'En New', 'de' => 'De New'], $updated->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'blue'], 'de' => ['color' => 'blau']], $updated->get('trans_array')->all());
    }
    
    public function testUpdateByIdMethodUsesDefaultConfiguredLocales()
    {
        $created = $this->repository->create([
            'trans' => ['en' => 'En', 'de' => 'De'],
            'trans_array' => ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
        ]);
        
        $updated = $this->repository->updateById(1, [
            'trans' => ['en' => 'En New', 'de' => 'De New'],
            'trans_array' => ['en' => ['color' => 'blue'], 'de' => ['color' => 'blau']],
        ]);
        
        $this->assertSame(['en' => 'En New'], $updated->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'blue']], $updated->get('trans_array')->all());
    }
    
    public function testUpdateByIdMethodSpecificLocale()
    {
        $this->repository->locale('de');
        $this->repository->locales('en', 'de');
        
        $created = $this->repository->create([
            'trans' => ['en' => 'En', 'de' => 'De'],
            'trans_array' => ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
        ]);
        
        $updated = $this->repository->updateById(1, [
            'trans->de' => 'De New',
            'trans_array->de' => ['color' => 'blau'],
        ]);
        
        $this->assertSame(['en' => 'En', 'de' => 'De New'], $updated->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'red'], 'de' => ['color' => 'blau']], $updated->get('trans_array')->all());
        
        $updated = $this->repository->updateById(1, [
            'trans_array->de->color' => 'grün',
        ]);
        
        $this->assertSame(['en' => ['color' => 'red'], 'de' => ['color' => 'grün']], $updated->get('trans_array')->all());
    }
    
    public function testUpdateMethodOnlyUsesConfiguredLocales()
    {
        $this->repository->locale('de');
        $this->repository->locales('en', 'de');
        
        $created = $this->repository->create([
            'trans' => ['en' => 'En', 'de' => 'De'],
            'trans_array' => ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
        ]);
        
        $updated = $this->repository->update(where: [], attributes: [
            'trans' => ['en' => 'En New', 'de' => 'De New', 'fr' => 'Fr New'],
            'trans_array' => ['en' => ['color' => 'blue'], 'de' => ['color' => 'blau'], 'fr' => ['color' => 'bleu']],
        ])->first();
        
        $this->assertSame(['en' => 'En New', 'de' => 'De New'], $updated->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'blue'], 'de' => ['color' => 'blau']], $updated->get('trans_array')->all());
    }
    
    public function testUpdateMethodUsesDefaultConfiguredLocales()
    {
        $created = $this->repository->create([
            'trans' => ['en' => 'En', 'de' => 'De'],
            'trans_array' => ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
        ]);
        
        $updated = $this->repository->update(where: [], attributes: [
            'trans' => ['en' => 'En New', 'de' => 'De New'],
            'trans_array' => ['en' => ['color' => 'blue'], 'de' => ['color' => 'blau']],
        ])->first();
        
        $this->assertSame(['en' => 'En New'], $updated->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'blue']], $updated->get('trans_array')->all());
    }
    
    public function testUpdateMethodSpecificLocale()
    {
        $this->repository->locale('de');
        $this->repository->locales('en', 'de');
        
        $created = $this->repository->create([
            'trans' => ['en' => 'En', 'de' => 'De'],
            'trans_array' => ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
        ]);
        
        $updated = $this->repository->update(where: [], attributes: [
            'trans->de' => 'De New',
            'trans_array->de' => ['color' => 'blau'],
        ])->first();
        
        $this->assertSame(['en' => 'En', 'de' => 'De New'], $updated->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'red'], 'de' => ['color' => 'blau']], $updated->get('trans_array')->all());
        
        $updated = $this->repository->update(where: [], attributes: [
            'trans_array->de->color' => 'grün',
        ])->first();
        
        $this->assertSame(['en' => ['color' => 'red'], 'de' => ['color' => 'grün']], $updated->get('trans_array')->all());
    }
    
    public function testUpdateMethodWhereCurrentLocale()
    {
        $this->repository->locale('de');
        $this->repository->locales('en', 'de');
        
        $created = $this->repository->create([
            'trans' => ['en' => 'En', 'de' => 'De'],
            'trans_array' => ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
        ]);
        
        $updated = $this->repository->update(where: ['trans' => 'De'], attributes: [
            'trans->de' => 'De New',
            'trans_array->de' => ['color' => 'blau'],
        ])->first();
        
        $this->assertSame(['en' => 'En', 'de' => 'De New'], $updated->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'red'], 'de' => ['color' => 'blau']], $updated->get('trans_array')->all());
    }
    
    public function testUpdateMethodWhereSepcificLocale()
    {
        $this->repository->locale('de');
        $this->repository->locales('en', 'de');
        
        $created = $this->repository->create([
            'trans' => ['en' => 'En', 'de' => 'De'],
            'trans_array' => ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
        ]);
        
        $updated = $this->repository->update(where: ['trans->en' => 'En'], attributes: [
            'trans->de' => 'De New',
            'trans_array->de' => ['color' => 'blau'],
        ])->first();
        
        $this->assertSame(['en' => 'En', 'de' => 'De New'], $updated->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'red'], 'de' => ['color' => 'blau']], $updated->get('trans_array')->all());
    }
    
    public function testDeleteMethodWhereCurrentLocale()
    {
        $this->repository->locale('de');
        $this->repository->locales('en', 'de');
        
        $created = $this->repository->create([
            'trans' => ['en' => 'En', 'de' => 'De'],
            'trans_array' => ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
        ]);
        
        $deleted = $this->repository->delete(where: ['trans' => 'De'])->first();
        
        $this->assertSame(['en' => 'En', 'de' => 'De'], $deleted->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'red'], 'de' => ['color' => 'rot']], $deleted->get('trans_array')->all());
    }
    
    public function testDeleteMethodWhereSepcificLocale()
    {
        $this->repository->locale('de');
        $this->repository->locales('en', 'de');
        
        $created = $this->repository->create([
            'trans' => ['en' => 'En', 'de' => 'De'],
            'trans_array' => ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']],
        ]);
        
        $deleted = $this->repository->delete(where: ['trans->en' => 'En'])->first();
        
        $this->assertSame(['en' => 'En', 'de' => 'De'], $deleted->get('trans')->all());
        $this->assertSame(['en' => ['color' => 'red'], 'de' => ['color' => 'rot']], $deleted->get('trans_array')->all());
    }
}