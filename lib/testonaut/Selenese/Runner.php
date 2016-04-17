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
use testonaut\Selenese\Test;
use testonaut\Compare;
use testonaut\Image;
use testonaut\Matrix;
use testonaut\Page;
use testonaut\Selenese\Command\captureEntirePageScreenshot;
use testonaut\Selenese\Command\Pause;
use testonaut\Config;
use testonaut\Settings\Browser;
use testonaut\Utils\Javascript;


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
   * Runner constructor.
   * @param $profiles
   * @param Page $tests
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

  /**
   * @param array $tests
   * @return array
   */
  public function run(array $tests) {
    $result = array();

    $bSettings = new Browser($this->page->getPath());

    for ($k = 0; $k < count($this->profiles); $k++) {
      for ($i = 0; $i < count($tests); $i++) {
        if (isset($this->profiles[$i]['name'])) {
          $name = str_replace(' ', '_', $this->profiles[$i]['name']).'_'
            .str_replace(' ', '_', $this->profiles[$i]['browser']);
        } else {
          $name = str_replace(' ', '_', $this->profiles[$i]['platform'])
            .str_replace(' ', '_', $this->profiles[$i]['browser'])
            .$this->profiles[$i]['version'];
        }

        $test = new Test();

        if (isset($bSettings->settings['browser']['active']) &&
            in_array($name, $bSettings->settings['browser']['active'])) {
          $url = $bSettings->settings['browser']['urls'][$name];
          $test->setBaseUrl($url);
        }

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

  /**
   * @param $test
   * @param $profile
   * @param Page $page
   * @return array
   */
  protected function _run( $test, $profile, Page $page) {
    $this->imageDir = $page->getImagePath();
    $pageConf = $page->config();
    $res = array();
    $capabilities = $this->getCapabilities($profile);

    $hub = Config::getInstance()->seleniumHub;
    $webDriver = \RemoteWebDriver::create($hub, $capabilities, 5000);

    $pollFile = Config::getInstance()->Path."/tmp/".$this->page->getPath();

    $webDriver = $this->setDriverOption($webDriver, $profile);
    $i = 0;
    foreach ($test->commands as $command) {

      $commandStr = str_replace('testonaut\Selenese\Command\\', '', get_class($command));
      $commandStr = str_replace(' ', '', $commandStr);
      try {
        if ($commandStr == 'captureEntirePageScreenshot') {
          $srcImage = $this->getPath($profile) . "/" . $command->arg1;
          $this->takeScreenshot($profile, $webDriver, $srcImage);

          $compareObj = new Compare();
          $compare = $compareObj->compare($profile, $command->arg1, $page->getPath(), $this->imageDir );
          $res = $compareObj->compareResult($compare, $res, $command->arg1);
        } else {
          $commandResult = $command->runWebDriver($webDriver);
        }

        if ($pageConf['screenshots'] == 'step') {
          $imageName = "step_".$i.".png";
          $srcImage = $this->getPath($profile) .'/'. $imageName;
          $this->takeScreenshot($profile, $webDriver, $srcImage);

          $compareObj = new Compare();
          $compare = $compareObj->compare($profile, $imageName, $page->getPath(), $this->imageDir);
          $res = $compareObj->compareResult($compare, $res, "step_".$i.".png");
        }

      } catch (\Exception $e) {
        $commandResult = new CommandResult(FALSE, FALSE, $e->getMessage());
      }

      if ($commandResult->success) {
        $res[] = array(TRUE, $commandResult->message, $commandStr);
      } else {
        $res[] = array(FALSE, $commandResult->message, $commandStr);
        $browserResult = FALSE;
      }

      error_log($test->getPath());
      $this->polling($pollFile, array($test->getPath(), $res));

      if ($commandResult->continue === FALSE) {
        break;
      }
      $i++;
    }

    if ($pageConf['screenshots'] == 'test') {
      $srcImage = $this->getPath($profile) . "/afterTest.png";
      $this->takeScreenshot($profile, $webDriver, $srcImage);

      $compareObj = new Compare();
      $compare = $compareObj->compare($profile, 'afterTest.png', $page->getPath(), $this->imageDir);
      $res = $compareObj->compareResult($compare, $res, 'afterTest.png');
      $this->polling($pollFile, $res);
    }

    $webDriver->quit();
    $matrix = new Matrix($page, $this->browser);
    $matrix->writeResult($res, $profile);

    unlink($pollFile);

    return $res;
  }

  private function polling($pollFile, $res) {
    file_put_contents($pollFile, json_encode($res));
  }

  private function takeScreenshot($profile, $webDriver, $srcImage) {

    if ($profile['browser'] == "chrome") {
      $javascript = new Javascript($webDriver);
      $javascript->invokeHtml2Canvas();
      $javascript->invokeNanoajax();
      sleep(3);
      $javascript->invokeTakeScreenshot($srcImage);
      sleep(5);

    } else {
      $screenCommand = new CaptureEntirePageScreenshot();
      $screenCommand->arg1 = $srcImage;
      $screenCommand->runWebDriver($webDriver);
      sleep(2);
    }
  }

  protected function getProfileName($profile) {
    if (isset($profile['browser'])) {
      if (isset($profile['name'])) {
        $profileName = $profile['name'] . '_' . $profile['browser'];
      } else {
        $profileName = $profile['browser'] . '_default';
      }
    }
    return $profileName;
  }

  private function getPath($profile) {

    $profileName = $this->getProfileName($profile);

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
            '--user-data-dir='.sys_get_temp_dir(),
          ));

        $options = $this->setExperimentalOption($profile, $options);
        $capabilities->setCapability(\ChromeOptions::CAPABILITY, $options);
      }

      if ($browserName == "MicrosoftEdge") {}

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

}
