<?php

namespace phpSelenium\Settings;

use phpSelenium\Page;

class Browser {
  private $setting;
  private $page;

  public function __construct($path) {
    $this->page= new Page($path);;
    $this->setting = $this->getSettings();
  }

  public function getSettings(){
    $settings = $this->page->config();
    return $settings['browser'];
  }

  public function setSettings(array $browser){
    $page = new Page($this->path);
    $this->setting = $browser;
    $page->config($this->setting);
  }

}