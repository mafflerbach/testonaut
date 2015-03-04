<?php

namespace phpSelenium\Settings;

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
    if (isset($settings['type'])) {
      for ($i = 0; $i < count($this->type); $i++) {
        if ($settings['type'] == $this->type[$i]) {
          $return[$this->type[$i]] = true;
        } else {
          $return[$this->type[$i]] = false;
        }
      }
    } else {
      $return = array(
        'static' => true,
        'suite' => false,
        'test' => false,
        'project' => false,
      );
    }
    return $return;
  }

  public function setSettings($type) {

    if (in_array($type, $this->type)) {
      $this->setting['type'] = $type;
    } else {
      throw new \Exception('Bad Page Type');
    }
    return $this->page->config($this->setting);
  }

  protected function getBrowserList() {
    $browser = new \phpSelenium\Parser\Config\Browser();
    $browser->config(\phpSelenium\Config::getInstance()->seleniumConsole);
    return $browser;
  }

}