<?php
namespace phpSelenium\Selenium;

use phpSelenium\Config;
use phpSelenium\Parser\Config\Browser;

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
          $browsers[] = $data['request']['capabilities'][$k];
        }
      } else {
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
    $apiString = $this->seleniumAddress . "/" . $endpoints;
    $content = file_get_contents($apiString);
    return json_decode($content, TRUE);
  }
}