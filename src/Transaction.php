<?php

namespace CBM\Model;

use PDO;
use Closure;
use Exception;

class Transaction
{
    public static function run(PDO $pdo, Closure $callback): mixed
    {
        try{
            $pdo->beginTransaction();

            $result = $callback($pdo);

            $pdo->commit();

            return $result;
        }catch (Exception $e){
            $pdo->rollBack();
            throw $e;
        }
    }
}