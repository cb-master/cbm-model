<?php
/**
 * APP Name:        Laika DB Model
 * APP Provider:    Showket Ahmed
 * APP Link:        https://cloudbillmaster.com
 * APP Contact:     riyadtayf@gmail.com
 * APP Version:     1.0.0
 * APP Company:     Cloud Bill Master Ltd.
 */

// Forbidden Access
defined('ROOTPATH') || http_response_code(403).die('403 Forbidden Access!');

// Config File Resources
function config_resources():string
{
    return "<?php
/**
 * APP Name:        Laika DB Model
 * APP Provider:    Showket Ahmed
 * APP Link:        https://cloudbillmaster.com
 * APP Contact:     riyadtayf@gmail.com
 * APP Version:     1.0.0
 * APP Company:     Cloud Bill Master Ltd.
 */

// Forbidden Access
defined('ROOTPATH') || http_response_code(403).die('403 Forbidden Access!');

// Database Host
define('DB_HOST', 'localhost');

// Database Port
define('DB_PORT', 3306);

// Database Driver
define('DB_DRIVER', 'mysql');

// Database Name
define('DB_NAME', 'test');

// Database User
define('DB_USER', 'root');

// Database Password
define('DB_PASSWORD', '');

// Database Fetch Limit
define('LIMIT', 20);";
}

