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

namespace Tobento\Service\Repository\Storage\Test\Helper;

use DateTime;
use Exception;

class Dates
{
    public static function isDateFormat(string $format, mixed $value)
    {
        $date = DateTime::createFromFormat('!'.$format, $value);

        if ($date && $date->format($format) == $value) {
            return true;
        }
        
        return false;
    }
    
    public static function isTimestamp(mixed $value)
    {
        try {
            new DateTime('@'.$value);
        } catch(Exception $e) {
            return false;
        }
        
        return true;
    }
}