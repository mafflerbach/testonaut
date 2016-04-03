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


namespace testonaut\Selenese;

use testonaut\Capabilities;
use testonaut\Image;
use testonaut\Matrix;
use testonaut\Page;
use testonaut\Selenese\Command\captureEntirePageScreenshot;

class Runner {

  protected $tests;
  protected $platform;
  protected $browser;
  protected $version;
  protected $config;
  protected $configFile;
  protected $imageDir = NULL;


  protected $profiles;


  /**
   * @param $dir
   * @param $config
   */
  public function __construct($profiles, Page $tests) {
    $this->profiles = $profiles;
    $this->tests = $this->collect($tests->transCodePath());
    $this->imageDir = $tests->getImagePath();
    $this->page = $tests;
  }

  protected function collect($outerDir, $tests = array()) {
    $paths = array();

    $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($outerDir), \RecursiveIteratorIterator::SELF_FIRST);
    foreach ($objects as $name => $object) {
      /**
       * @var \SplFileInfo $object
       */
      if (!$object->isDir() && $name != NULL) {
        $paths[] = $object;
      }
    }

    return $paths;
  }


  public function run($tests) {
    $result = array();

    for ($k = 0; $k < count($this->profiles); $k++) {
      for ($i = 0; $i < count($tests); $i++) {
        /**
         * @var \SplFileInfo $this ->tests
         */
        $test = new Test();

        $test->loadFromSeleneseHtml($tests[$i]);
        if ($test->commands == '') {
          continue;
        }

        $result[] = $this->_run($test, $this->profiles[$k], $tests[$i]);
      }
    }
    return $result;
  }


  /**
   * @param \RemoteWebDriver $driver
   * @return \RemoteWebDriver
   */
  private function setDriverOption(\RemoteWebDriver $driver, $profile) {
    if (isset($profile['driverOptions'])) {
      $option = json_decode($profile['driverOptions'], true);
      if (isset($option['dimensions'])) {
        $d = new \WebDriverDimension((int)$option['dimensions']['width'], (int)$option['dimensions']['height']);
        $driver->manage()->window()->setSize($d);
      }
    }
    return $driver;
  }

  protected function _run($test, $profile, Page $page) {
    $this->imageDir = $page->getImagePath();

    $capabilities = $this->getCapabilities($profile);
    $webDriver = \RemoteWebDriver::create("http://localhost:4444/wd/hub", $capabilities, 5000);
    $webDriver = $this->setDriverOption($webDriver, $profile);

    foreach ($test->commands as $command) {

      $commandStr = str_replace('testonaut\Selenese\Command\\', '', get_class($command));
      $commandStr = str_replace(' ', '', $commandStr);
      try {
        $commandResult = $command->runWebDriver($webDriver);
      } catch (\Exception $e) {
        $commandResult = new CommandResult(FALSE, FALSE, $e->getMessage());
      }

      if ($commandResult->success) {
        $res[] = array(TRUE, $commandResult->message, $commandStr);
      } else {
        $res[] = array(FALSE, $commandResult->message, $commandStr);
        $browserResult = FALSE;
      }

      // @TODO consider page settings for screenshots
      if ($commandStr == 'CaptureEntirePageScreenshot') {
        $srcImage = $this->getPath($profile) . "/" . $command->arg1;

        if ($profile['browser'] == "internet explorer") {
          $screenCommand = new CaptureEntirePageScreenshot();
          $screenCommand->arg1 = $srcImage;
          $screenCommand->runWebDriver($webDriver);
        } else {
          $webDriver->executeScript($this->getJs($srcImage), array());
        }
      }

      if ($commandResult->continue === FALSE) {
        break;
      }

    }
    $webDriver->quit();
    $matrix = new Matrix($page, $this->browser);
    $matrix->writeResult($res, $profile);

    return $res;
  }

  private function getPath($profile) {

    if (isset($profile['browser'])) {
      if (isset($profile['name'])) {
        $profileName = $profile['name'] . '_' . $profile['browser'];
      } else {
        $profileName = $profile['browser'] . '_default';
      }
    }

    if ($this->imageDir != NULL) {
      $path = $this->imageDir;
      $srcDir = $path . '/' . $profileName . "/src";
      $comp = $path . '/' . $profileName . "/comp";
      $ref = $path . '/' . $profileName . "/ref";

      if (!file_exists($srcDir)) {
        mkdir($srcDir, 0777, TRUE);
      }
      if (!file_exists($comp)) {
        mkdir($comp, 0777, TRUE);
      }
      if (!file_exists($ref)) {
        mkdir($ref, 0777, TRUE);
      }
    } else {
      $path = $this->imageDir . '/' . $profileName . "/src";
    }

    return $this->imageDir . '/' . $profileName . "/src";
  }

  private function setExperimentalOption($profile, \ChromeOptions $options) {
    if (isset($profile['capabilities'])) {
      $capabilities = json_decode($profile['capabilities'], true);

      if (isset($capabilities['experimental']) && isset($capabilities['experimental']['mobileEmulation'])) {
        $options = $options->setExperimentalOption("mobileEmulation", $capabilities['experimental']['mobileEmulation']);
      }
    }

    return $options;
  }

  private function getCapabilities($profile) {

    $DesiredCapabilities = new Capabilities();

    if (isset($profile['browser'])) {
      $browserName = $this->normalizeBrowserName($profile['browser']);
    } else {
      $browserName = $this->normalizeBrowserName($profile['browserName']);
    }

    if (method_exists($DesiredCapabilities, $browserName)) {
      $capabilities = $DesiredCapabilities::$browserName();

      if ($browserName == 'chrome') {
        $options = new \ChromeOptions();
        $options->addArguments(array(
            '--disable-web-security',
          ));
        $options->addArguments(array(
            '--user-data-dir=C:\Users\maren\AppData\Local\Temp',
          ));

        $options = $this->setExperimentalOption($profile, $options);
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

      if (isset($profile['version'])) {
        $capabilities->setVersion($profile['version']);
      }
      if (isset($profile['platform'])) {
        $capabilities->setPlatform($profile['platform']);
      }
    }

    return $capabilities;
  }


  private function normalizeBrowserName($browserString) {

    $browserString = str_replace('*', '', $browserString);

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

  private function getJs($srcImage) {

    $srcImage = str_replace('\\', '\\\\', $srcImage);
    $srcImage = str_replace('/', '\\\\', $srcImage);

    $js = "
      setTimeout(function () {
          var d = document;
          var script = d.createElement('script');
          script.type = 'text/javascript';
          script.src = 'https://localhost/testonaut/html2canvas.js';
          d.getElementsByTagName('head')[0].appendChild(script);
      }, 100);

      setTimeout(function () {
          var d = document;
          var script = d.createElement('script');
          script.type = 'text/javascript';
          script.src = 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js';
          d.getElementsByTagName('head')[0].appendChild(script);
      }, 100);
      setTimeout(function () {
        html2canvas(document.html, {
          onrendered: function(canvas) {

            $.ajax({
                method: 'POST',
                url: 'https://localhost/testonaut/server.php',
                xhrFields: {
                    withCredentials: true
                },
                data: { canvas: canvas.toDataURL('image/png'), path:'" . $srcImage . "'}
            })
            .done(function( msg ) {
            console.log(msg);
            });
          }})}, 500);";

    return $js;
  }
}