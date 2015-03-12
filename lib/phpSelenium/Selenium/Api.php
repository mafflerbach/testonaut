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


  public function test () {

    print_r($this->getBrowserName());

  }


  public function getBrowserName() {
    $pars = new Browser();
    $nodes = $pars->getNodes(@\file_get_contents(Config::getInstance()->seleniumConsole));
    $endpoint='grid/api/proxy?id=http://'.$nodes[0];
    $data = $this->getData($endpoint);
    return $data;
  }

  private function getData($endpoints) {
    $apiString = $this->seleniumAddress . "/" .$endpoints;
    $content =file_get_contents($apiString);
    return json_decode($content, true);
  }




}