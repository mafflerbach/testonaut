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

  public function __construct($page) {
    $this->page = new Page($page);
  }

  public function getMenu() {

    $user = new User();

    $request = new Request();

    $list = $this->list();

    if ($user->checkUser()) {
      return $list['private'];
    } else {
      return $list['public'];
    }
  }

  protected function list() {
    $request = new Request();
    $config = $this->page->config();


    $rules = array(
      'public' => $this->getPublic(),
      'private' => $this->getPrivate(),
      'fallback' => 'login/'
    );
    return $rules;
  }


  private function getPublic() {
    $request = new Request();
    $config = $this->page->config();

    $recources = $this->resources();

    if ($config['type'] != 'static' && $config['type'] != 'none') {
      $pub = array(
        $recources['root'],
        $recources['run'],
        $recources['history'],
        $recources['screenshots'],
      );
    } else {
      $pub = array(
        $recources['root'],
        $recources['history'],
      );
    }
    $priv[] = $recources['login'];
    return $pub;
  }

  private function getPrivate() {
    $request = new Request();
    $config = $this->page->config();

    $recources = $this->resources();

    if ($config['type'] != 'static' && $config['type'] != 'none') {
      $priv = array(
        $recources['root'],
        $recources['run'],
        $recources['edit'],
        $recources['history'],
        $recources['screenshots'],
        $recources['import'],
        $recources['config'],
        $recources['delete'],
      );
    } else {
      $priv = array(
        $recources['root'],
        $recources['edit'],
        $recources['history'],
        $recources['config'],
        $recources['delete'],
      );
    }

    $priv[] = $recources['logout'];
    return $priv;
  }

  private function resources() {

    $run = $this->getProfiles();

    $request = new Request();

    return array(
      'root' => array(
        'label' => 'root',
        'path' => '/'
      ),
      'globalconfig' => array(
        'label' => 'Globalconfig',
        'path' => 'globalconfig/'
      ),
      'run' => $run,
      'edit' => array(
        'label' => 'Edit',
        'path' => 'edit/'.$request->getPath()
      ),
      'history' => array(
        'label' => 'History',
        'path' => 'history/'
      ),
      'screenshots' => array(
        'label' => 'Screenshot',
        'path' => 'screenshot/'
      ),
      'import' => array(
        'label' => 'Import',
        'path' => 'import/'
      ),
      'config' => array(
        'label' => 'Config',
        'path' => 'config/'
      ),
      'delete' => array(
        'label' => 'Delete',
        'path' => 'delete/'
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
        'badge' => $node['platform'],
        'version' => $node['version'],
        'label' => $node['browserName'],
        'path' => 'run/'.$request->getPath() .''. $node['browserName'],
      );

      if ($node['version'] != '') {
        $push[$k]['path'] .= '/' . $node['version'];
      } else {
        $push[$k]['path'] .= '/default';
      }

      if ($node['platform'] != '') {
        $push[$k]['path'] .= '/' . $node['platform'];
      } else {
        $push[$k]['path'] .= '/default';
      }
      $k++;
    }

    $push[] = array(
      'label' => 'ALL',
      'path' => 'run/all',
    );

    return $push;

  }


}