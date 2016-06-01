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


namespace testonaut\Selenium\Webdriver;


use testonaut\Capabilities;
use testonaut\Config;
use testonaut\Settings\Emulator\Devices;


class Setup {

  private $hub = '';
  private $profile = '';
  private $deviceData = '';
  private $type = '';

  public function __construct($profile) {

    $this->profile = $profile;
    $this->hub = Config::getInstance()->seleniumHub;
    $this->getDeviceData();

  }

  public function init() {
    $capabilities = $this->getCapabilities();
    $webDriver = \RemoteWebDriver::create($this->hub, $capabilities, 5000);
    $webDriver = $this->setDriverOption($webDriver);

    return $webDriver;
  }

  /**
   * @param \RemoteWebDriver $driver
   * @return \RemoteWebDriver
   */
  public function setDriverOption(\RemoteWebDriver $driver) {

    if ($this->deviceData != '') {

      $d = new \WebDriverDimension(
        (int)$this->deviceData['screen'][$this->type]['width'],
        (int)$this->deviceData['screen'][$this->type]['height']
      );
      $driver->manage()->window()->setSize($d);
    }

    if (isset($this->profile['driverOptions'])) {
      $option = json_decode($this->profile['driverOptions'], true);
      if (isset($option['dimensions'])) {
        $d = new \WebDriverDimension((int)$option['dimensions']['width'], (int)$option['dimensions']['height']);
        $driver->manage()->window()->setSize($d);
      }
    }
    return $driver;
  }


  private function setExperimentalOption(\ChromeOptions $options) {
    if (isset($this->profile['capabilities'])) {
      $capabilities = json_decode($this->profile['capabilities'], true);

      if (isset($capabilities['experimental']) && isset($capabilities['experimental']['mobileEmulation'])) {
        $options = $options->setExperimentalOption("mobileEmulation", $capabilities['experimental']['mobileEmulation']);
      }
    }

    return $options;
  }


  public function getCapabilities() {

    $DesiredCapabilities = new Capabilities();

    if (isset($this->profile['browser'])) {
      $browserName = $this->normalizeBrowserName($this->profile['browser']);
    } else {
      $browserName = $this->normalizeBrowserName($this->profile['browserName']);
    }

    if (method_exists($DesiredCapabilities, $browserName)) {
      $capabilities = $DesiredCapabilities::$browserName();

      if ($browserName == 'chrome') {
        $options = new \ChromeOptions();
        $options->addArguments(array(
          '--disable-web-security',
        ));
        $options->addArguments(array(
          '--user-data-dir=' . sys_get_temp_dir(),
        ));
        if ($this->deviceData != '') {
          $options->addArguments(array(
            '--user-agent=' . $this->deviceData['user-agent'],
          ));
        }

        $capabilities->setCapability(\ChromeOptions::CAPABILITY, $options);
      }

      if ($browserName == "MicrosoftEdge") {
      }

      if ($browserName == 'firefox') {

        $options = new \FirefoxProfile();
        $options->setPreference('security.fileuri.strict_origin_policy', FALSE);
        $options->setPreference('network.http.referer.XOriginPolicy', 1);

        $capabilities->setCapability(\FirefoxDriver::PROFILE, $options);
      }

      if (isset($this->profile['version'])) {
        $capabilities->setVersion($this->profile['version']);
      }
      if (isset($this->profile['platform'])) {
        $capabilities->setPlatform($this->profile['platform']);
      }
    }

    return $capabilities;
  }

  private function normalizeBrowserName($browserString) {

    $browserString = str_replace('*', '', $browserString);
    $browserString = str_replace('default', '', $browserString);

    if (strpos($browserString, ' ') > 0) {
      $expl = explode(' ', $browserString);
      $browserName = $expl[0] . ucfirst($expl[1]);
    } else if (strpos($browserString, '_') > 0) {
      $expl = explode('_', $browserString);
      $browserName = $expl[0] . ucfirst($expl[1]);
    } else {
      $browserName = $browserString;
    }
    return $browserName;
  }


  private function getDeviceData() {

    $deviceName = json_decode($this->profile['capabilities'], true);
    if (isset($deviceName['experimental'])) {
      $deviceName = $deviceName['experimental']['mobileEmulation']['deviceName'];

      if (strpos($deviceName, 'portrait') !== FALSE) {
        $this->type = 'vertical';
      }
      if (strpos($deviceName, 'landscape') !== FALSE) {
        $this->type = 'horizontal';
      }

      $devices = new Devices();
      $name = str_replace('_portrait', '', $deviceName);
      $name = str_replace('_landscape', '', $name);
      $name = str_replace('_', ' ', $name);

      return $this->deviceData = $devices->getDevicesByName($name);
    }
  }

  public function getPixelRatio() {
    return $this->deviceData['screen']['device-pixel-ratio'];
  }

}