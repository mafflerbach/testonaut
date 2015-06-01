<?php

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

