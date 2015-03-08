<?php

namespace phpSelenium\Settings;

use phpSelenium\Page as PageHandler;

class Browser {
  private $setting;
  private $page;

  public function __construct($path) {
    $this->page = new PageHandler($path);
    $this->settings = $this->page->config();
  }

  public function getSettings() {
    $settings = $this->page->config();
    $list = $this->getBrowserList();

    for ($i = 0; $i < count($list->browser); $i++) {
      if (isset($settings['browser']['active'])) {
        $active = $settings['browser']['active'];
        $browserName = $list->browser[$i]['browserName'];
        if (in_array($browserName, $active)) {
          $list->browser[$i]['active'] = $browserName;
        }
        $list->browser[$i]['url'] = $settings['browser']['urls'][$browserName];
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
    $this->setting['browser'] = $browser;
    return $this->page->config($this->setting);
  }

  protected function getBrowserList() {
    $browser = new \phpSelenium\Parser\Config\Browser();
    $browser->config(\phpSelenium\Config::getInstance()->seleniumConsole);
    return $browser;
  }

}