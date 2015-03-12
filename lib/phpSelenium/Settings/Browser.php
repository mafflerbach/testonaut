<?php

namespace phpSelenium\Settings;

use phpSelenium\Page as PageHandler;
use phpSelenium\Selenium\Api;

class Browser {
  private $setting;
  private $page;

  public function __construct($path) {
    $this->page = new PageHandler($path);
    $this->settings = $this->page->config();
  }

  public function getSettings() {
    $settings = $this->page->config();

    $api = new Api();
    $list = $api->getBrowserList();

    for ($i = 0; $i < count($list); $i++) {
      if (isset($settings['browser']['active'])) {
        $active = $settings['browser']['active'];
        $browserName = $list[$i]['browserName'];
        if (in_array($browserName, $active)) {
          $list[$i]['active'] = $browserName;
        }
        if (isset($settings['browser']['urls'][$browserName])) {
         $list[$i]['url'] = $settings['browser']['urls'][$browserName];
        }
      }
    }
    return $list;
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