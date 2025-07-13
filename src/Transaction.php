<?php

namespace CBM\Model;

use PDOException;
use Closure;

class Transaction Extends DB
{
    public static function run(Closure $callback, string $connection_name = 'default'): mixed
    {
        try{
            $db = self::getInstance($connection_name);
            $db->pdo->beginTransaction();
            $result = $callback($db);

            $db->pdo->commit();

            return $result;
        }catch(PDOException $e){
            $db->pdo->rollBack();
            throw $e;
        }
    }
}