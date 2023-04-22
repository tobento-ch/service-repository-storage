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

namespace Tobento\Service\Repository\Storage\Test\Mock;

use Tobento\Service\Repository\Storage\StorageWriteRepository;
use Tobento\Service\Repository\Storage\Column\ColumnsInterface;
use Tobento\Service\Repository\Storage\Column\ColumnInterface;
use Tobento\Service\Repository\Storage\Column;

/**
 * ProductWriteRepositoryWithColumns
 */
class ProductWriteRepositoryWithColumns extends StorageWriteRepository
{
    protected function configureColumns(): iterable|ColumnsInterface
    {
        return [
            Column\Id::new(),
            Column\Text::new('sku'),
            Column\FloatCol::new('price'),
        ];
    }
}