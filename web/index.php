<?php
/**
 *
 * GNU GENERAL PUBLIC LICENSE testonaut Copyright (C) 2015 Afflerbach
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 */
session_start();


$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('testonaut', __DIR__ . '/../lib/');
$loader->add('mafflerbach', __DIR__ . '/../lib/');

require_once('../lib/testonaut/Page/Provider/Gobalconfig.php');

$config = \testonaut\Config::getInstance();
$config->define('Path', dirname(__DIR__));
$config->define('debug', true);


$globalConf = new \testonaut\Page\Provider\Base();
$configuration = $globalConf->getConfig();

$db = new testonaut\Utils\Db('../index.db');

  
$seleniumAddress = "http://localhost:4444";
if (isset($configuration['cache'])) {
  $config->define('Cache', $configuration['cache']);
} else {
  $config->define('Cache', '');
}
$config->define('appPath', $configuration['appPath']);
$config->define('seleniumHub', $seleniumAddress.'/wd/hub');

$config->define('wikiPath', dirname(__DIR__) . '/root');
$config->define('imageRoot', dirname(__DIR__) . '/web/images');
$config->define('fileRoot', dirname(__DIR__) . '/web/files');
$config->define('result', dirname(__DIR__) . '/result');
$config->define('seleniumConsole', $seleniumAddress.'/grid/console');
$config->define('seleniumAddress', $seleniumAddress);
$config->define('domain', $_SERVER['HTTP_HOST']);
$config->define('db', $db);
$config->define('templates', dirname(__DIR__).'/template/' );



/*
 *
 * $app->mount('/', new testonaut\Page\Provider\Start());
$app->mount('/edit/', new testonaut\Page\Provider\Start(true));
$app->mount('/image/', new testonaut\Page\Provider\Image());
$app->mount('/files/{path}', new testonaut\Page\Provider\File());
$app->mount('/import/{path}', new testonaut\Page\Provider\Import());
$app->mount('/globalconfig/', new testonaut\Page\Provider\Globalconfig());
$app->mount('/edit/{path}', new testonaut\Page\Provider\Edit());
$app->mount('/history/{path}', new testonaut\Page\Provider\History());
$app->mount('/screenshot/{path}', new testonaut\Page\Provider\Screenshot());
$app->mount('/config/{path}', new testonaut\Page\Provider\Config());
$app->mount('/delete/{path}', new testonaut\Page\Provider\Delete());
$app->mount('/run/{path}', new testonaut\Page\Provider\Run());
$app->mount('/user/', new testonaut\Page\Provider\User());
$app->mount('/login/', new testonaut\Page\Provider\Login());
$app->mount('/logout/', new testonaut\Page\Provider\Logout());
$app->mount('/reset/', new testonaut\Page\Provider\Reset());
$app->mount('/{path}/', new testonaut\Page\Provider\Page());
*/

$rules = array (
  'public' => array(
    '/', 'history/', 'run/', 'screenshot/'
  ),
  'private' => array(
    'edit/', 'image/', 'files/', 'import/', 'globalconfig/', 'config/', 'delete/', 'logout/', 'user/', 'image/'
  ),
  'fallback' => 'login/'
);

$security = new \testonaut\Security\Provider();
$security->setFirewall($rules);

$debug = new \testonaut\Debug\Provider();
$debug->observe('\mafflerbach\Routing');

$routing = new \mafflerbach\Routing();


$routing->push('(login)', new \testonaut\Page\Provider\Login());
$routing->push('(logout)', new \testonaut\Page\Provider\Logout());
$routing->push('(reset)', new \testonaut\Page\Provider\Reset());
$routing->push('edit/.*', new \testonaut\Page\Provider\Edit());
$routing->push('search/.*', new \testonaut\Page\Provider\Ajax());
$routing->push('config/.*', new \testonaut\Page\Provider\Config());
$routing->push('history/.*', new \testonaut\Page\Provider\History());
$routing->push('screenshot/.*', new \testonaut\Page\Provider\Screenshot());
$routing->push('(globalconfig)', new \testonaut\Page\Provider\Globalconfig());
$routing->push('run/.*', new \testonaut\Page\Provider\Run());
$routing->push('.*$', new \testonaut\Page\Provider\Start());
$routing->before($security);
$routing->after($debug);
$routing->execute($debug);
