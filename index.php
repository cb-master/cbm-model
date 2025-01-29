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
print_r(Model::table('test1')
                ->column('id','INT(11) unsigned not null auto_increment')
                ->column('username','varchar(255) null')
                ->primary('id')
                ->create());
echo '</pre>';