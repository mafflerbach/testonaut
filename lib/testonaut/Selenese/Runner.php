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
use testonaut\Selenese\Command\Pause;
use testonaut\Config;


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
    $pageConf = $page->config();
    $res = array();
    $capabilities = $this->getCapabilities($profile);

    $hub = Config::getInstance()->seleniumHub;
    $webDriver = \RemoteWebDriver::create($hub, $capabilities, 5000);

    $webDriver = $this->setDriverOption($webDriver, $profile);
    $i = 0;
    foreach ($test->commands as $command) {

      $commandStr = str_replace('testonaut\Selenese\Command\\', '', get_class($command));
      $commandStr = str_replace(' ', '', $commandStr);
      try {
        $commandResult = $command->runWebDriver($webDriver);

        if ($pageConf['screenshots'] == 'step') {
          $imageName = "/step_".$i.".png";
          $srcImage = $this->getPath($profile) .'/'. $imageName;
          $this->takeScreenshot($profile, $webDriver, $srcImage);
          $compare = $this->compare($profile, $imageName, $page->getPath());
          $res = $this->compareResult($compare, $res, "step_".$i.".png");
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

      if ($commandStr == 'captureEntirePageScreenshot') {
        $srcImage = $this->getPath($profile) . "/" . $command->arg1;
        $this->takeScreenshot($profile, $webDriver, $srcImage);
        $compare = $this->compare($profile, $command->arg1, $page->getPath());

        $res = $this->compareResult($compare, $res, $command->arg1);
      }

      if ($commandResult->continue === FALSE) {
        break;
      }
      $i++;
    }

    if ($pageConf['screenshots'] == 'test') {
      $srcImage = $this->getPath($profile) . "/afterTest.png";
      $this->takeScreenshot($profile, $webDriver, $srcImage);
      $compare = $this->compare($profile, 'afterTest.png', $page->getPath());

      $res = $this->compareResult($compare, $res, 'afterTest.png');
    }

    $webDriver->quit();
    $matrix = new Matrix($page, $this->browser);
    $matrix->writeResult($res, $profile);

    return $res;
  }

  private function compareResult($compare, $res, $imageName) {
    if ($compare) {
      $res[] = array(TRUE, 'Compare Image '.$imageName, 'Compare Success');
    } else {
      $res[] = array(FALSE, 'Compare Images'.$imageName, 'Compare Fail');
    }
    return $res;
  }

  protected function compare($profile, $imgName, $pagePath) {

    $profileName = $this->getProfileName($profile);

    $imageDir = $this->imageDir;
    $path = $imageDir . '/' . $profileName . "/src/" . $imgName;
    $pathref = $imageDir . '/' . $profileName. "/ref/" . $imgName;
    $comp = $imageDir . '/' . $profileName . "/comp/" . $imgName;

    if (file_exists($pathref)) {
      if (file_exists($comp)) {
        unlink($comp);
      }
      if (class_exists('\\Imagick')) {
        $compare = new Image();

        $result = $compare->compare($path, $pathref, $comp);
      } else {
        $result = FALSE;
      }
    } else {
      $result = FALSE;
    }

    $this->writeCompareToDb($pagePath,$path, $pathref, $comp, $result);

    return $result;

  }

  private function writeCompareToDb($path,$src, $pathref, $comp, $result) {
    $db = new \testonaut\Utils\Db(Config::getInstance()->Path . '/index.db');
    $this->db = $db->getInstance();

    $date = new \DateTime();
    $isoDate = $date->format(\DateTime::ISO8601);
    $images = json_encode(array($src, $pathref, $comp));

    $sql = "insert into imageCompare (date, path, result, images) values (:date, :path, :result, :images)";
    $stm = $this->db->prepare($sql);
    $stm->bindParam(':date', $isoDate);
    $stm->bindParam(':path', $path);
    $stm->bindParam(':result', $result);
    $stm->bindParam(':images', $images);
    $stm->execute();

  }


  private function takeScreenshot($profile, $webDriver, $srcImage) {
    if ($profile['browser'] == "internet explorer") {
      $screenCommand = new CaptureEntirePageScreenshot();
      $screenCommand->arg1 = $srcImage;
      $screenCommand->runWebDriver($webDriver);
    } else {
      $webDriver->executeScript($this->getJs($srcImage), array());
    }

    $pause = new Command\Pause();
    $pause->arg1 = 4000;
    $pause->runWebDriver($webDriver);

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

  private function getJs($srcImage) {


    if (DIRECTORY_SEPARATOR == '\\') {
      $srcImage = str_replace('\\', '\\\\', $srcImage);
      $srcImage = str_replace('/', '\\\\', $srcImage);
    } else {
      $srcImage = str_replace(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, $srcImage);
    }

    $js = "
      setTimeout(function () {
          var d = document;
          var script = d.createElement('script');
          script.type = 'text/javascript';
          script.src = 'https://".$_SERVER['SERVER_ADDR']."/testonaut/html2canvas.js';
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
                url: 'https://".$_SERVER['SERVER_ADDR']."/testonaut/server.php',
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
