<?php

use CBM\Model\Model;

define('ROOTPATH', __DIR__);

require_once(ROOTPATH.'/vendor/autoload.php');


Model::config([
    'host'      =>  'localhost',
    'name'      =>  'test',
    'user'      =>  'root',
    'password'  =>  ''
]);

echo '<pre>';
print_r(Model::table('test')->where(['id'=>2],'>')->filter('id','<',4)->pop());
echo '</pre>';