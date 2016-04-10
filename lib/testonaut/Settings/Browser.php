<?php
/**
 *
 * GNU GENERAL PUBLIC LICENSE testonaut Copyright (C) 2015 Afflerbach
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 */


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
    $list = $this->getBrowserList();

    $profile = new Profile();
    $custom = $profile->getCustomProfiles();

    $list = array_merge($list, $custom);
    
    for ($i = 0; $i < count($list); $i++) {
      $browserName ="";
      if (isset($list[$i]['platform'])) {
        $browserName .= $list[$i]['platform'];
      }
      if (isset($list[$i]['browserName'])) {
        $browserName .= $list[$i]['browserName'] = str_replace(' ', '_', $list[$i]['browserName']);
      } else {
        $browserName .= $list[$i]['name'] = str_replace(' ', '_', $list[$i]['name']);
        $browserName .= "_".$list[$i]['browser'] = str_replace(' ', '_', $list[$i]['browser']);
      }
      if (isset($list[$i]['version'])) {
        $browserName .= $list[$i]['version'];
      }

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

  protected function getBrowserList() {

    $api = new Api();

    return $api->getBrowserList();
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