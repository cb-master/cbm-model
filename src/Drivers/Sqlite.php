<?php
/**
 * Laika Database Model
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * This file is part of the Laika PHP MVC Framework.
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace CBM\Model\Drivers;

class Sqlite
{
    public function buildDSN(array $config): string
    {
        $path = $config['path'] ?? ':memory:';
        return "sqlite:{$path}";
    }
}