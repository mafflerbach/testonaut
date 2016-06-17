<?php
/**
 *
 * GNU GENERAL PUBLIC LICENSE testonautterm Copyright (C) 2016 Afflerbach
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 */

namespace testonaut\Command\Line;


use testonaut\Capabilities;
use testonaut\Command\Line;
use testonaut\Selenese\Command\CaptureEntirePageScreenshot;
use testonaut\Selenese\CommandResult;
use testonaut\Utils\Javascript;

class Runner {

  protected $args;
  protected $tests;
  protected $platform;
  protected $browser;
  protected $version;
  protected $config;
  protected $configFile;
  protected $imageDir = NULL;

  /**
   * @param $dir
   * @param $config
   */
  public function __construct($args, $config) {
    $this->tests = $this->collect($args['d']);

    if (isset($args['i']) != NULL) {
      $this->imageDir = $args['i'];
    }

    if (isset($args['c']) != NULL) {
      $this->configFile = $args['c'];
    }

    $this->args = $args;

    $this->config = $config;

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


  public function run() {

    for ($i = 0; $i < count($this->tests); $i++) {
      /**
       * @var \SplFileInfo $this ->tests
       */
      $test = new \testonaut\Command\Line\Test();
      $test->loadFromSeleneseHtml($this->tests[$i]);

      if ($test->commands == '') {
        continue;
      }
      $this->_run($test);
    }
  }

  /**
   * @param \RemoteWebDriver $driver
   * @return \RemoteWebDriver
   */
  private function setDriverOption(\RemoteWebDriver $driver) {

    if (isset($this->config['dimension'])) {
      $d = new \WebDriverDimension((int)$this->config['dimension']['width'], (int)$this->config['dimension']['height']);
      $driver->manage()->window()->setSize($d);
    }

    if(isset($this->config['fullscreen']) && $this->config['fullscreen']  == true) {
      $driver->manage()->window()->maximize();
    }

    return $driver;
  }

  protected function _run(Test $test) {

    $capabilities = $this->getCapabilities();
    $webDriver = \RemoteWebDriver::create($this->args['s'], $capabilities, 5000);

    $webDriver = $this->setDriverOption($webDriver);

    foreach ($test->commands as $command) {

      $commandStr = str_replace('testonaut\Selenese\Command\\', '', get_class($command));
      $commandStr = str_replace(' ', '', $commandStr);
      try {
        $commandResult = $command->runWebDriver($webDriver);
      } catch (\Exception $e) {
        $commandResult = new CommandResult(FALSE, FALSE, $e->getMessage());
      }

      if ($commandResult->success) {
        $res[] = array(
          true,
          $commandResult->message,
          $commandStr
        );
        print(".");
      } else {
        $res[] = array(
          false,
          $commandResult->message,
          $commandStr
        );
        $browserResult = FALSE;
        print("F");
      }

      if ($commandStr == 'CaptureEntirePageScreenshot') {
        $srcImage = $this->getPath($test) . "/" . $command->arg1;

        if ($this->config['name'] != "chrome") {
          $screenCommand = new CaptureEntirePageScreenshot();
          $screenCommand->arg1 = $srcImage;
          $screenCommand->runWebDriver($webDriver);
        } else {
          $javascript = new Javascript($webDriver);
          $javascript->invokeHtml2Canvas();
          $javascript->invokeNanoajax();
          sleep(2);
          $javascript->invokeTakeScreenshot($srcImage, $webDriver);
        }
      }

      if ($commandResult->continue === FALSE) {
        break;
      }
    }
    $webDriver->quit();
  }

  public function invokeTakeScreenshot($srcImage,$webDriver) {
    if (DIRECTORY_SEPARATOR == '\\') {
      $srcImage = str_replace('\\', '\\\\', $srcImage);
      $srcImage = str_replace('/', '\\\\', $srcImage);
    } else {
      $srcImage = str_replace(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, $srcImage);
    }

    $js = "
      setTimeout(function () {
        html2canvas(document.html, {
          onrendered: function(canvas) {
          qwest.post('https://" . $_SERVER['SERVER_NAME'] . "/testonaut/server.php', {
              canvas: canvas.toDataURL('image/png'),
              path:'" . $srcImage . "'
              }).then(function(xhr, response) {})
          }
        })
      }, 1500);";

    $webDriver->executeScript($js, array());
    sleep(5);
    return $this->webDriver;
  }



  private function getPath(Test $test) {
    if ($this->imageDir != NULL) {
      $path = $this->imageDir . '/' . $test->file->getPath();
      if (!file_exists($path)) {
        mkdir($path, 0777, true);
      }
    } else {
      $path = $test->file->getPath();
    }

    return $path;
  }

  private function getCapabilities() {

    $DesiredCapabilities = new Capabilities();
    $mobileEmulation = '';

    if (isset($this->config['deviceName'])) {
      $browserName = 'chrome';
    } else {
      $browserName = $this->normalizeBrowserName($this->config['name']);
    }

    if (method_exists($DesiredCapabilities, $browserName)) {
      $capabilities = $DesiredCapabilities::$browserName();
      if ($this->config['name'] == 'chrome') {
        $options = new \ChromeOptions();
        $options->addArguments(array(
          '--disable-web-security',
        ));
        $options->addArguments(array(
          '--user-data-dir=' . sys_get_temp_dir(),
        ));


        if (isset($this->config['deviceName'])) {
          $mobileEmulation = array("deviceName" => $this->config['deviceName']);
          $options->setExperimentalOption("mobileEmulation", $mobileEmulation);
        } else {
          $browserName = $this->normalizeBrowserName($this->config['name']);
        }

        $capabilities->setCapability(\ChromeOptions::CAPABILITY, $options);
      }

      if ($this->config['name'] == "MicrosoftEdge") {

      }

      if ($this->config['name'] == 'firefox') {

        $options = new \FirefoxProfile();
        $options->setPreference('security.fileuri.strict_origin_policy', false);
        $options->setPreference('network.http.referer.XOriginPolicy', 1);

        $capabilities->setCapability(\FirefoxDriver::PROFILE, $options);
      }

      if (isset($this->config['version'])) {
        $capabilities->setVersion($this->config['version']);
      }
      if (isset($this->config['platform'])) {
        $capabilities->setPlatform($this->config['platform']);
      }
    }

    return $capabilities;
  }


  private function normalizeBrowserName($browserString) {

    $browserString = str_replace('*', '', $browserString);

    if (strpos($browserString, ' ') > 0) {
      $expl = explode(' ', $browserString);
      $browserName = $expl[0] . ucfirst($expl[1]);
    } else if (strpos($browserString, ' ') > 0) {
      $expl = explode(' ', $browserString);
      $browserName = $expl[0] . ucfirst($expl[1]);
    } else {
      $browserName = $browserString;
    }

    return $browserName;
  }


}