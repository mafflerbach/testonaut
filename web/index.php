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

$config = \testonaut\Config::getInstance();
$config->define('Path', dirname(dirname(__FILE__)));

require_once('../lib/testonaut/Page/Provider/Gobalconfig.php');

$globalConf = new \testonaut\Page\Provider\Globalconfig();
$configuration = $globalConf->getConfig();
$db = new testonaut\Utils\Db('../index.db');


$seleniumAddress = $configuration['seleniumAddress'];
$config->define('Cache', $configuration['cache']);
$config->define('appPath', $configuration['appPath']);
$config->define('wikiPath', dirname(dirname(__FILE__)) . '/root');
$config->define('imageRoot', dirname(dirname(__FILE__)) . '/web/images');
$config->define('fileRoot', dirname(dirname(__FILE__)) . '/web/files');
$config->define('result', dirname(dirname(__FILE__)) . '/result');
$config->define('seleniumHub', $seleniumAddress.'/wd/hub');
$config->define('seleniumConsole', $seleniumAddress.'/grid/console');
$config->define('seleniumAddress', $seleniumAddress);
$config->define('domain', $_SERVER['HTTP_HOST']);
$config->define('db', $db);

$app = new Silex\Application();
$app['debug'] = true;
if(isset($configuration['theme'])) {
$app['theme'] = $configuration['theme'];
$config->define('theme', $configuration['theme']);


} else {
    $app['theme'] = 'bootstrap';
$config->define('theme', 'bootstrap');
}


/*
$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
  'http_cache.cache_dir' => __DIR__.'/cache/',
)); */
$app->register(new Silex\Provider\TwigServiceProvider(), array(
  'twig.path' => __DIR__ . '/views',
  //'twig.options'    => array('cache' => __DIR__ . '/cache')
));
$app->register(new Silex\Provider\RoutingServiceProvider());

use Silex\Provider\CsrfServiceProvider;
$app->register(new CsrfServiceProvider());



$app->mount('/', new testonaut\Page\Provider\Start());
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

if ($app['debug']) {
  $app->run();
}
else{
  $app['http_cache']->run();
}

