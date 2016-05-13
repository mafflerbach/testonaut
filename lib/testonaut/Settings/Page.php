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
use testonaut\Utils\Git;

/**
 * Class Page
 *
 * @package testonaut\Settings
 */
class Page {

  /**
   * @private
   */
  private $setting;
  /**
   * @private \testonaut\Page
   */
  private $page;
  /**
   * @private array
   */
  private $types = array(
    'static',
    'suite',
    'test',
    'project'
  );
  /**
   * @private array
   */
  private $screenshots = array(
    'step',
    'test',
    'none'
  );

  public $configuration;

  /**
   * @param $path
   */
  public function __construct($path) {

    $this->page = new \testonaut\Page($path);
    $this->settings = $this->page->config();
  }

  /**
   * @return string type
   */
  public function getType() {

    $settings = $this->page->config();
    for ($i = 0; $i < count($this->types); $i++) {
      if ($settings['type'] == $this->types[$i]) {
        return $this->types[$i];
      }
    }
  }

  /**
   * return an array with all existing types.
   * The valid setting is equals true in the settings array
   *
   * @return array
   */
  public function getSettings() {

    $settings = $this->page->config();
    $return = array();
    if (isset($settings['type'])) {
      for ($i = 0; $i < count($this->types); $i++) {
        if ($settings['type'] == $this->types[$i]) {
          $return[$this->types[$i]] = TRUE;
        } else {
          $return[$this->types[$i]] = FALSE;
        }
      }
    } else {
      $return = array(
        'static'  => TRUE,
        'suite'   => FALSE,
        'test'    => FALSE,
        'project' => FALSE,
      );
    }

    return $return;
  }

  /**
   * return an array with all existing screenshot settings.
   * The valid setting is equals true in the settings array
   *
   * @return array
   */
  public function getScreenshotSettings() {

    $settings = $this->page->config();
    $return = array();

    if (isset($settings['screenshots'])) {
      for ($i = 0; $i < count($this->screenshots); $i++) {
        if ($settings['screenshots'] == $this->screenshots[$i]) {
          $return[$this->screenshots[$i]] = TRUE;
        } else {
          $return[$this->screenshots[$i]] = FALSE;
        }
      }
    } else {
      $return = array(
        'step' => FALSE,
        'test' => FALSE,
        'none' => TRUE
      );
    }

    return $return;
  }

  /**
   * @param string $types [static|suite|test|project]
   * @return bool
   * @throws \Exception
   */
  public function setSettings($type) {

    if (in_array($type, $this->types)) {
      $this->setting['type'] = $type;
    } else {
      throw new \Exception('Bad Page Type');
    }

    return $this->page->config($this->setting);
  }

  /**
   * @param string $settings [step|test|none]
   * @return bool
   * @throws \Exception
   */
  public function setScreenshotSettings($settings) {

    if (in_array($settings, $this->screenshots)) {
      $this->setting['screenshots'] = $settings;
    } else {
      throw new \Exception('Bad screenshot Settings ');
    }

    return $this->page->config($this->setting);
  }

  public function setOriginUrl($url) {
    $this->setting['originUrl'] = $url;
    $dir = $this->page->getProjectRoot();
    $git = new Git($dir);
    $git->setOriginUrl($url);

    return $this->page->config($this->setting);

  }

  public function getOriginUrl() {
    $settings = $this->page->config();
    if (isset($settings['originUrl'])) {
      return $settings['originUrl'];
    }
    return '';
  }

}