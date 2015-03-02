<?php

namespace phpSelenium\Settings;

use phpSelenium\Page;

class Browser {
  private $setting;
  private $page;

  public function __construct($path) {
    $this->page = new Page($path);
    $this->settings = $this->page->config();
  }

  public function getSettings() {
    $settings = $this->page->config();
    $list = $this->getBrowserList();
    for ($i = 0; $i < count($list->browser); $i++) {
      if (isset($settings->browser)) {
        $browserName = $list->browser[$i]['browserName'];
        if (property_exists($settings->browser, $browserName)) {
          $list->browser[$i]['active'] = $settings->browser->$browserName;
        }
      }
    }
    return $list->browser;
  }

  /**
   * $browser = array(
   *  'browser' => array (
   *    'firefox' => true,
   *    'chrome' => true
   *  )
   * );
   *
   * @param array $browser
   */

  public function setSettings(array $browser) {
    $page = new Page($this->path);
    $this->setting['browser'] = $browser;
    $page->config($this->setting);
  }

  protected function getBrowserList() {
    $browser = new \phpSelenium\Parser\Config\Browser();
    $browser->config(\phpSelenium\Config::getInstance()->seleniumConsole);
    return $browser;
  }

}