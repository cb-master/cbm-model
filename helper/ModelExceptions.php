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

// Forbidden Access
defined('ROOTPATH') || http_response_code(403).die('403 Forbidden Access!');

use Exception;

// Forbidden Access
defined('ROOTPATH') || http_response_code(403).die('403 Forbidden Access!');

final class ModelExceptions extends Exception
{
    // Exception Message
    public function message():string
    {
        return "[".$this->getCode() . "] - " . $this->getMessage() . "... Line: " . $this->getLine() . ":" . $this->getLine();
    }
}
