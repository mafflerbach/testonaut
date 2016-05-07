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

//require_once('../lib/testonaut/Page/Provider/Gobalconfig.php');

//$globalConf = new \testonaut\Page\Provider\Globalconfig();
//$configuration = $globalConf->getConfig();

$db = new testonaut\Utils\Db('../index.db');

$config = \testonaut\Config::getInstance();
$config->define('Path', dirname(__DIR__));

$seleniumAddress = "http://localhost:4444";
//$config->define('Cache', $configuration['cache']);
//$config->define('appPath', $configuration['appPath']);
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





$routing = new \mafflerbach\Routing();

$routing->push('test/', new \testonaut\Page\Provider\Test());
$routing->push('/', new \testonaut\Page\Provider\Start());
$routing->push('login/', new \testonaut\Page\Provider\Login());
$routing->push('reset/', new \testonaut\Page\Provider\Reset());
//$routing->before(new \testonaut\Security\Provider());
$routing->execute();
