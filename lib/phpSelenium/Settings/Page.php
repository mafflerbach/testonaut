<?php

namespace phpSelenium\Settings;

use phpSelenium\Selenese\Exception\Exception;

class Page {
  private $setting;
  private $page;
  private $type = array(
    'static',
    'suite',
    'test',
    'project'
  );

  public function __construct($path) {
    $this->page = new \phpSelenium\Page($path);
    $this->settings = $this->page->config();
  }

  public function getSettings() {
    $settings = $this->page->config();
    $return = array();

    for ($i = 0; $i < count($this->type); $i++) {
      if ($settings->type == $this->type[$i]) {
        $return[$this->type[$i]] = true;
      } else {
        $return[$this->type[$i]] = false;
      }
    }

    return $return;
  }

  public function setSettings($type) {
    $page = new \phpSelenium\Page($this->path);

    if (in_array($type, $this->type)) {
      $this->setting['type'] = $type;
    } else {
      throw new \Exception('Bad Page Type');
    }

    $page->config($this->setting);
  }

  protected function getBrowserList() {
    $browser = new \phpSelenium\Parser\Config\Browser();
    $browser->config(\phpSelenium\Config::getInstance()->seleniumConsole);
    return $browser;
  }

}