<?php

namespace phpSelenium\Settings;

use phpSelenium\Selenium\Api;

class Page {
  private $setting;
  private $page;
  private $types = array(
    'static',
    'suite',
    'test',
    'project'
  );
  private $screenshots= array(
    'step',
    'test',
    'none'
  );

  public function __construct($path) {
    $this->page = new \phpSelenium\Page($path);
    $this->settings = $this->page->config();
  }

  public function getType() {
    $settings = $this->page->config();
    for ($i = 0; $i < count($this->types); $i++) {
      if ($settings['type'] == $this->types[$i]) {
        return $this->types[$i];
      }
    }
  }

  protected function getProjectPage() {
    $path = $this->page->getPath();
  }

  public function getProjectSettings() {

    $path = $this->getProjectPage();

    $page = new \phpSelenium\Page($path);
    $settings = $page->config();

  }

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

  public function setSettings($types) {

    if (in_array($types, $this->types)) {
      $this->setting['type'] = $types;
    } else {
      throw new \Exception('Bad Page Type');
    }
    return $this->page->config($this->setting);
  }

  public function setScreenshotSettings($settings) {
    if (in_array($settings, $this->screenshots)) {
      $this->setting['screenshots'] = $settings;
    } else {
      throw new \Exception('Bad screenshot Settings ');
    }
    return $this->page->config($this->setting);
  }
}