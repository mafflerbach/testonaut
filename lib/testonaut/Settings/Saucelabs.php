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


namespace testonaut\Settings;


use testonaut\Config;

class Saucelabs {

  private $platforms;

  public function __construct() {
    $cacheFile = Config::getInstance()->Path . '/saucelabsPlatforms.json';
    if (file_exists($cacheFile)) {
      $time = filemtime($cacheFile);
      $timeplus1w = $time + 604800;
      $date = new \DateTime();
      if ($timeplus1w <= $date->getTimestamp()) {
        file_put_contents($cacheFile, file_get_contents('http://saucelabs.com/rest/v1/info/platforms/webdriver'));
      }
    } else {
      file_put_contents($cacheFile, file_get_contents('http://saucelabs.com/rest/v1/info/platforms/webdriver'));
    }
    $this->platforms = json_decode(file_get_contents($cacheFile), true);
  }

  public function getSupportedSettings() {
    return $this->prepSuportedSettings();
  }

  private function prepSuportedSettings() {

    $array = array();

    for ($i = 0, $j = count($this->platforms); $i < $j; $i++) {
      if (!isset($this->platforms[$i]['device'])) {
        $array[$this->platforms[$i]['os']][$this->platforms[$i]['api_name']][] = $this->platforms[$i]['short_version'];
      }

    }

    return $array;

  }


}