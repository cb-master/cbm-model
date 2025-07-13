<?php

namespace CBM\Model;

use PDOException;
use Closure;

class Transaction
{
    public static function run(Closure $callback, string $connection_name = 'default'): mixed
    {
        try{
            $pdo = ConnectionManager::get($connection_name);
            $pdo->beginTransaction();

            $result = $callback($pdo);

            $pdo->commit();

            return $result;
        }catch(PDOException $e){
            $pdo->rollBack();
            throw $e;
        }
    }
}