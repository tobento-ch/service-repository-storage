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
 * WriteRepositoryCastTest
 */
abstract class WriteRepositoryCastTest extends TestCase
{
    protected null|WriteRepositoryInterface $repository = null;

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
    
    public function testCreateMethod()
    {
        $created = $this->repository->create([
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
        
        $this->assertIsInt($created->get('id'));
        $this->assertIsBool($created->get('bool'));
        $this->assertTrue(Dates::isDateFormat('Y-m-d H:i:s', $created->get('datetime')));
        $this->assertTrue(Dates::isDateFormat('Y-m-d', $created->get('date')));
        $this->assertTrue(Dates::isDateFormat('H:i:s', $created->get('time')));
        $this->assertTrue(Dates::isTimestamp($created->get('timestamp')));
        $this->assertIsFloat($created->get('float'));
        $this->assertIsInt($created->get('int'));
        $this->assertIsArray($created->get('json'));
        $this->assertIsString($created->get('text'));
        $this->assertTrue($created->get('trans') instanceof StringTranslations);
        $this->assertTrue($created->get('trans_array') instanceof ArrayTranslations);
    }
    
    public function testUpdateByIdMethod()
    {
        $created = $this->repository->create(['id' => 1]);
        
        $updated = $this->repository->updateById(1, [
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
        
        $this->assertIsInt($updated->get('id'));
        $this->assertIsBool($updated->get('bool'));
        $this->assertTrue(Dates::isDateFormat('Y-m-d H:i:s', $updated->get('datetime')));
        $this->assertTrue(Dates::isDateFormat('Y-m-d', $updated->get('date')));
        $this->assertTrue(Dates::isDateFormat('H:i:s', $updated->get('time')));
        $this->assertTrue(Dates::isTimestamp($updated->get('timestamp')));
        $this->assertIsFloat($updated->get('float'));
        $this->assertIsInt($updated->get('int'));
        $this->assertIsArray($updated->get('json'));
        $this->assertIsString($updated->get('text'));
        $this->assertTrue($updated->get('trans') instanceof StringTranslations);
        $this->assertTrue($updated->get('trans_array') instanceof ArrayTranslations);
    }
    
    public function testUpdateMethod()
    {
        $created = $this->repository->create(['id' => 1]);
        
        $updated = $this->repository->update(where: [], attributes: [
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
        ])->first();
        
        $this->assertIsInt($updated->get('id'));
        $this->assertIsBool($updated->get('bool'));
        $this->assertTrue(Dates::isDateFormat('Y-m-d H:i:s', $updated->get('datetime')));
        $this->assertTrue(Dates::isDateFormat('Y-m-d', $updated->get('date')));
        $this->assertTrue(Dates::isDateFormat('H:i:s', $updated->get('time')));
        $this->assertTrue(Dates::isTimestamp($updated->get('timestamp')));
        $this->assertIsFloat($updated->get('float'));
        $this->assertIsInt($updated->get('int'));
        $this->assertIsArray($updated->get('json'));
        $this->assertIsString($updated->get('text'));
        $this->assertTrue($updated->get('trans') instanceof StringTranslations);
        $this->assertTrue($updated->get('trans_array') instanceof ArrayTranslations);
    }
}