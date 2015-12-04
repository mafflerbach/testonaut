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



namespace testonaut\Parser\Config;

use testonaut\Config;
use testonaut\Selenese\Exception\Exception;

class Browser {

  public $browser;

  public function __construct() {
  }

  public function getNodes($str) {

    $dom = new \DOMDocument();
    @$dom->loadHTML($str);
    $nodes = array();
    $xpath = new \DOMXPath($dom);
    $nodeList = $xpath->query('//div[@type="config"]/p[contains(., "url:")]');
    foreach ($nodeList as $node) {
      $nodes[] = str_replace('url:', '', $node->nodeValue);
    }

    return $nodes;
  }
}

