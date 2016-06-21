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


namespace testonaut\Selenium;

use testonaut\Config;
use testonaut\Parser\Config\Browser;

class Api {

  private $seleniumAddress;

  public function __construct() {

    $hub = Config::getInstance();
    $this->seleniumAddress = $hub->seleniumAddress;
  }

  private function getNodeInformations() {

    $pars = new Browser();

    $cacheFile = Config::getInstance()->Path . '/hubCache';

    if (!file_exists($cacheFile) || filemtime($cacheFile) >= time() + 60 * 15) {

      $hub = @\file_get_contents(Config::getInstance()->seleniumConsole);
      if (Config::getInstance()->Cache) {
        file_put_contents($cacheFile, $hub);
      }
    } else {
      $hub = @\file_get_contents($cacheFile);
    }

    $nodes = $pars->getNodes($hub);

    $browsers = array();

    for ($i = 0; $i < count($nodes); $i++) {
      $endpoint = 'grid/api/proxy?id=' . $nodes[$i];
      $data = $this->getData($endpoint);
      if (count($data['request']['capabilities']) > 1) {

        for ($k = 0; $k < count($data['request']['capabilities']); $k++) {
          if (!isset($data['request']['capabilities'][$k]['version'])) {
            $data['request']['capabilities'][$k]['version'] = '';
          }
          $browsers[] = $data['request']['capabilities'][$k];
        }
      } else {
        if (!isset($data['request']['capabilities'][0]['version'])) {
          $data['request']['capabilities'][0]['version'] = '';
        }
        $browsers[] = $data['request']['capabilities'][0];
      }
    }

    return $browsers;
  }

  public function getBrowserList() {

    $data = $this->getNodeInformations();

    return $data;
  }

  private function getData($endpoints) {
    // @TODO implement caching;
    $apiString = $this->seleniumAddress . "/" . $endpoints;
    $content = file_get_contents($apiString);
    return json_decode($content, TRUE);
  }
}