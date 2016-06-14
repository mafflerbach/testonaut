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


namespace testonaut\Generate;


use mafflerbach\Http\Request;
use testonaut\Page;
use testonaut\Settings\Profile;
use testonaut\User;

class Menu {
  private $page;
  private $path;
  private $context;
  private $test = false;
  private $type = '';


  public function __construct($page) {
    $this->page = new Page($page);
    $this->path = $page;
  }

  public function getMenu($context) {
    $this->context = $context;
    $user = new User();

    $request = new Request();

    $list = $this->listing();

    if ($user->checkUser() || $this->test) {
      return $list['private'];
    } else {
      return $list['public'];
    }
  }

  protected function listing() {
    $request = new Request();

    $rules = array(
      'public' => $this->getPublic(),
      'private' => $this->getPrivate(),
      'fallback' => 'login/'
    );
    return $rules;
  }

  protected function config($config = NULL) {
    if ($config != NULL) {
      return $config;
    } else {
      return $this->page->config();
    }
  }


  private function getPublic() {
    $request = new Request();

    $config = $this->config();

    $resources = $this->resources();

    if ($config['type'] != 'static' && $config['type'] != 'none') {
      $pub = array(
        $resources['root'],
      );
      if ($this->context != 'run') {
        $pub[] = $resources['run'];
      }
      if ($this->context != 'history') {
        $pub[] = $resources['history'];
      }
      if ($this->context != 'screenshots') {
        $pub[] = $resources['screenshots'];
      }
    } else {
      $pub = array(
        $resources['root'],
      );
      if ($this->context != 'history') {
        $pub[] = $resources['history'];
      }
      if ($this->context != 'screenshots') {
        $pub[] = $resources['screenshots'];
      }
    }
    $pub[] = $resources['login'];
    return $pub;
  }

  private function getPrivate() {
    $request = new Request();
    $config = $this->config();

    $resources = $this->resources();

    if ($config['type'] != 'static' && $config['type'] != 'none') {
      $private = array(
        $resources['root'],
      );

      if ($this->context != 'edit') {
        $private[] = $resources['edit'];
      }
      if ($this->context != 'run') {
        $private[] = $resources['run'];
      }
      if ($this->context != 'history') {
        $private[] = $resources['history'];
      }
      if ($this->context != 'screenshots') {
        $private[] = $resources['screenshots'];
      }
      if ($this->context != 'config') {
        $private[] = $resources['config'];
      }


    } else {
      $private = array(
        $resources['root'],
      );

      if ($this->context != 'globalconfig') {
        $private[] = $resources['globalconfig'];
      }
      if ($this->context != 'edit') {
        $private[] = $resources['edit'];
      }
      if ($this->context != 'history') {
        $private[] = $resources['history'];
      }
      if ($this->context != 'config') {
        $private[] = $resources['config'];
      }
    }

    $private[] = $resources['logout'];
    return $private;
  }

  private function resources() {

    $run = $this->getProfiles();

    $request = new Request();

    return array(
      'root' => array(
        'label' => 'root',
        'path' => ''
      ),
      'globalconfig' => array(
        'label' => 'Globalconfig',
        'path' => 'globalconfig'
      ),
      'run' => $run,
      'edit' => array(
        'label' => 'Edit',
        'path' => 'edit/' . $this->path
      ),
      'history' => array(
        'label' => 'History',
        'path' => 'history/' . $this->path
      ),
      'screenshots' => array(
        'label' => 'Screenshot',
        'path' => 'screenshot/' . $this->path
      ),
      'import' => array(
        'label' => 'Import',
        'path' => 'import/' . $this->path
      ),
      'config' => array(
        'label' => 'Config',
        'path' => 'config/' . $this->path
      ),
      'delete' => array(
        'label' => 'Delete',
        'path' => 'delete/' . $this->path
      ),
      'logout' => array(
        'label' => 'Logout',
        'path' => 'logout/'
      ),
      'login' => array(
        'label' => 'Login',
        'path' => 'login/'
      ),
    );
  }

  private function getProfiles() {

    $base = new Page\Provider\Base();
    $globalConf = $base->getConfig();
    $browserSettings = new Profile();
    $browsers = $browserSettings->get();
    $request = new Request();

    $push = array(
      'label' => 'Run',
      'path' => 'run/'
    );
    $k = 0;

    foreach ($browsers['grid'] as $node) {
      
      $push[$k] = array(
        'label' => $node['browserName'],
        'path' => 'run/' . $this->path . '/' . $node['browserName'],
      );

      if (isset($node['platform'])) {
        $push[$k]['badge'] = $node['platform'];
      }

      if (isset($node['version'])) {
        $push[$k]['version'] = $node['version'];
      }

      if (isset($node['version']) && $node['version'] != '') {
        $push[$k]['path'] .= '/' . $node['version'];
      } else {
        $push[$k]['path'] .= '/default';
      }

      if (isset($node['platform']) && $node['platform'] != '') {
        $push[$k]['path'] .= '/' . $node['platform'];
      } else {
        $push[$k]['path'] .= '/default';
      }
      $k++;
    }

    foreach ($browsers['custom'] as $node) {
      var_dump($node['local']);
      if ($globalConf['useSaucelabs'] == 0 && $node['local'] == 1) {
        $push[] = array(
          'label' => $node['name'],
          'badge' => $node['browser'],
          'path' => 'run/' . $this->path . '/' . $node['name']
        );
      } else if($globalConf['useSaucelabs'] == 1) {
        $push[] = array(
          'label' => $node['name'],
          'badge' => $node['browser'],
          'path' => 'run/' . $this->path . '/' . $node['name']
        );
      }
    }
    
    if (isset($browsers['saucelabs']) && $globalConf['useSaucelabs'] == 1) {
      foreach ($browsers['saucelabs'] as $key => $browser) {
        foreach ($browser as $browserName => $versions) {
          for ($i = 0, $j = count($versions); $i < $j; $i++) {
            $push[] = array(
              'version' => $browserName.' '.$versions[$i],
              'badge' => $key,
              'path' => 'run/' . $this->path . '/' . $browserName . '/' .$versions[$i] . '/' . urldecode($key)
            );
          }
        }
      }
    }

    $push[] = array(
      'label' => 'ALL',
      'path' => 'run/' . $this->path . '/all',
    );

    return $push;
  }

  protected function test($value) {
    $this->test = $value;
  }

  protected function type($value) {
    $this->test = $value;
  }

}