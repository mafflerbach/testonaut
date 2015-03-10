<?php

namespace phpSelenium\Parser\Config;

use phpSelenium\Config;
use phpSelenium\Selenese\Exception\Exception;

class Browser {
  public $browser;

  public function __construct() {

  }

  public function config($url = "") {
    $page = @\file_get_contents(Config::getInstance()->seleniumConsole);

    if ($page === FALSE ) {
      throw new \Exception("Can't reach " . $url);
    }

    return $this->parse($page);
  }

  protected function parse($str) {
    $dom = new \DOMDocument();
    @$dom->loadHTML($str);

    $xpath = new \DOMXPath($dom);
    $nodeList = $xpath->query('//div[@type="config"]/p[contains(., "browser:")]');
    $browserList = array();
    $browserCapList = array();
    foreach ($nodeList as $node) {
      $browserCab = str_replace('browser:', '', $node->nodeValue);
      $arrBrowserCab = explode(',', $browserCab);
      for ($i = 0; $i < count($arrBrowserCab); $i++) {
        $tmp = explode('=', $arrBrowserCab[$i]);
        $browserList[$tmp[0]] = $tmp[1];
      }
      $browserCapList[] = $browserList;
    }
    $this->browser = $browserCapList;
    return $browserCapList;
  }
}

