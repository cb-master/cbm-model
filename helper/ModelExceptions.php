<?php
/**
 * APP Name:        Laika DB Model
 * APP Provider:    Showket Ahmed
 * APP Link:        https://cloudbillmaster.com
 * APP Contact:     riyadtayf@gmail.com
 * APP Version:     1.0.0
 * APP Company:     Cloud Bill Master Ltd.
 */

// Namespace
namespace CBM\ModelHelper;

use Exception;

final class ModelExceptions extends Exception
{
    // Errors
    private static $errors = [];

    // Set Error
    public static function set(string $message, int|string $code, string $file, int|string $line):bool
    {
        self::$errors[] = [
            'message'   =>  trim($message),
            'code'      =>  trim($code ?: 1000),
            'file'      =>  trim($file),
            'line'      =>  trim($line)
        ];
        return true;
    }

    // Throw New Error
    public static function throw(object $e):bool
    {
        self::$errors[] = [
            'message'   =>  $e->message,
            'code'      =>  $e->code ?: 1000,
            'file'      =>  $e->file,
            'line'      =>  $e->line
        ];
        return true;
    }

    // Get Errors
    public static function errors():array
    {
        return self::$errors;
    }

    // Exception Message
    public function message():string
    {
        return "[<b>{$this->getCode()}</b>] - {$this->getMessage()}. In {$this->getFile()}:{$this->getLine()}<br>";
    }
}
