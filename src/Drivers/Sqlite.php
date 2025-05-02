<?php

namespace CBM\Model\Drivers;

class Sqlite
{
    public function buildDSN(array $config): string
    {
        $path = $config['path'] ?? ':memory:';
        return "sqlite:{$path}";
    }
}