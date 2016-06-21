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


namespace lib\testonaut\Generate;


use testonaut\Generate\Menu;

$loader = require __DIR__ . '/../../../../vendor/autoload.php';
$loader->add('testonaut', __DIR__ . '/../../../../lib/');
$loader->add('mafflerbach', __DIR__ . '/../../../../lib/');


class MenuTest extends \PHPUnit_Framework_TestCase {
  public static function setUpBeforeClass() {
    $config = \testonaut\Config::getInstance();
    $seleniumAddress = 'http://localhost:4444';
    $config->define('wikiPath', '..\..\..\root');
    $config->define('Path', dirname(dirname(__FILE__)));
    $config->define('seleniumAddress', $seleniumAddress);
    $config->define('seleniumConsole', $seleniumAddress . '/grid/console');
    $config->define('seleniumAddress', $seleniumAddress);
    $config->define('Cache', FALSE);
  }


  public function testGetMenuPublic() {

    $array = array(
      array(
        'label' => 'root',
        'path' => ''
      ),
      array(
        'label' => 'Run',
        'path' => 'run/',
        array(
          'label' => 'ALL',
          'path' => 'run//all',
        )
      ),
      array(
        'label' => 'History',
        'path' => 'history/'
      ),
      array(
        'label' => 'Screenshot',
        'path' => 'screenshot/'
      ),
      array(
        'label' => 'Login',
        'path' => 'login/'
      ),
    );
    $menu = new Menu('');
    $menuRes = $menu->getMenu('');
    $this->assertEquals($array, $menuRes);

  }

  public function testGetMenuPrivate() {

    $array = array(
      array(
        'label' => 'root',
        'path' => ''
      ),
      array(
        'label' => 'Edit',
        'path' => 'edit/'
      ),
      array(
        'label' => 'Run',
        'path' => 'run/',
        array(
          'label' => 'ALL',
          'path' => 'run//all',
        )
      ),
      array(
        'label' => 'History',
        'path' => 'history/'
      ),
      array(
        'label' => 'Screenshot',
        'path' => 'screenshot/'
      ),
      array(
        'label' => 'Config',
        'path' => 'config/'
      ),
      array(
        'label' => 'Logout',
        'path' => 'logout/'
      ),
    );

    $menu = new MenuProxy('');
    $menu->test(true);
    $menuRes = $menu->getMenu('');
    $this->assertEquals($array, $menuRes);
  }

  public function testGetMenuProject() {

    $array = array(
      array(
        'label' => 'root',
        'path' => ''
      ),
      array(
        'label' => 'Edit',
        'path' => 'edit/'
      ),
      array(
        'label' => 'Run',
        'path' => 'run/',
        array(
          'label' => 'ALL',
          'path' => 'run//all',
        )
      ),
      array(
        'label' => 'History',
        'path' => 'history/'
      ),
      array(
        'label' => 'Screenshot',
        'path' => 'screenshot/'
      ),
      array(
        'label' => 'Config',
        'path' => 'config/'
      ),
      array(
        'label' => 'Logout',
        'path' => 'logout/'
      ),
    );

    $menu = new MenuProxy('');
    $menu->test(true);
    $menu->config(array('type' => 'project'));
    $menuRes = $menu->getMenu('');
    $this->assertEquals($array, $menuRes);
  }
}

class MenuProxy extends Menu {
  public function test($value) {
    parent::test(true);
  }

  public function config($value = null) {
    return parent::config($value);
  }
}
