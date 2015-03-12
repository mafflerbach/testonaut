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
    $nodes = $pars->getNodes(@\file_get_contents(Config::getInstance()->seleniumConsole));
    $endpoint='grid/api/proxy?id='.$nodes[0];
    $data = $this->getData($endpoint);
    return $data;
  }

  public function getBrowserList() {
    $data = $this->getNodeInformations();
    return $data['request']['capabilities'];
  }

  private function getData($endpoints) {
    $apiString = $this->seleniumAddress . "/" .$endpoints;
    $content =file_get_contents($apiString);
    return json_decode($content, true);
  }




}