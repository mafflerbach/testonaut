<?php

namespace testonaut\Settings;

use testonaut\Page as PageHandler;
use testonaut\Selenium\Api;

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
      $browserName = $list[$i]['browserName'] = str_replace(' ','_', $list[$i]['browserName']);

      if (isset($settings['browser']['active'])) {
        $active = $settings['browser']['active'];

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
}