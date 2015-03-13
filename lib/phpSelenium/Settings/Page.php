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

  public function setSettings($types) {

    if (in_array($types, $this->types)) {
      $this->setting['type'] = $types;
    } else {
      throw new \Exception('Bad Page Type');
    }
    return $this->page->config($this->setting);
  }
}