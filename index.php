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
print_r(Model::table('test')->not()->where(['id'=>10],'<')->update(['email'=>'new emails']));
echo '</pre>';