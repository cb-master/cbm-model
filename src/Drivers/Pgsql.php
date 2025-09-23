<?php
/**
 * Laika Database Model
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * This file is part of the Laika PHP MVC Framework.
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace CBM\Model\Drivers;

class Pgsql
{
    public function dsn(array $config):string
    {
        $host = $config['host'] ?? 'localhost';
        $dbname = $config['dbname'] ?? '';
        $port = $config['port'] ?? 5432;

        return "pgsql:host={$host};port={$port};dbname={$dbname}";
    }
}