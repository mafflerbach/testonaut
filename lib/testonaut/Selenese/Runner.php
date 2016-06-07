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

use testonaut\Compare;
use testonaut\Matrix;
use testonaut\Page;
use testonaut\Selenese\Command\captureEntirePageScreenshot;
use testonaut\Config;
use testonaut\Selenium\Webdriver\Setup;
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
  /**
   * @var Setup
   */
  protected $webdriverSetup = NULL;

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

    for ($i = 0; $i < count($tests); $i++) {
      for ($k = 0; $k < count($this->profiles); $k++) {
        if (isset($this->profiles[$k]['name'])) {
          $name = str_replace(' ', '_', $this->profiles[$k]['name']) . '_' . str_replace(' ', '_', $this->profiles[$k]['browser']);
        } else {
          $name = str_replace(' ', '_', $this->profiles[$k]['platform']) . str_replace(' ', '_', $this->profiles[$k]['browser']) . $this->profiles[$k]['version'];
        }

        $test = new Test();

        if (isset($bSettings->settings['browser']['active']) && in_array($name, $bSettings->settings['browser']['active'])) {
          $url = $bSettings->settings['browser']['urls'][$name];
          $test->setBaseUrl($url);
        }

        $test->loadFromSeleneseHtml($tests[$i]);
        if ($test->commands == '') {
          continue;
        }

        $tmp = array(
          'result' => $this->_run($test, $this->profiles[$k], $tests[$i]),
          'path' => $test->getPath()
        );
        $result[] = $tmp;
      }
    }
    return $result;
  }


  /**
   * @param $test
   * @param $profile
   * @param Page $page
   * @return array
   */
  protected function _run($test, $profile, Page $page) {
    $this->imageDir = $page->getImagePath();
    $pageConf = $page->config();
    $res = array();

    $this->webdriverSetup = new Setup($profile);
    $webDriver = $this->webdriverSetup->init();

    $pollFile = Config::getInstance()->Path . "/tmp/" . $this->page->getPath();

    $i = 0;
    foreach ($test->commands as $command) {

      $commandStr = str_replace('testonaut\Selenese\Command\\', '', get_class($command));
      $commandStr = str_replace(' ', '', $commandStr);

      try {
        if ($commandStr == 'captureEntirePageScreenshot') {
          $this->captureEntirePageScreenshot($profile, $webDriver, $page, $command->arg1, $res);
          continue;
        } else {

          $commandResult = $command->runWebDriver($webDriver);
          if ($commandStr == 'Open' || $commandStr == 'Click' || $commandStr == 'ClickAndWait') {
            $ratio = $this->webdriverSetup->getPixelRatio();
            $js = new Javascript($webDriver);
            $js->setPixelRatio($ratio);
          }
        }

        if ($pageConf['screenshots'] == 'step') {
          $imageName = "step_" . $i . ".png";
          $this->captureEntirePageScreenshot($profile, $webDriver, $page, $imageName, $res);
        }

      } catch (\Exception $e) {
        $commandResult = new CommandResult(FALSE, FALSE, $e->getMessage());
      }

      if ($commandResult->success) {
        $res[] = array(
          TRUE,
          $commandResult->message,
          $commandStr
        );
      } else {
        $res[] = array(
          FALSE,
          $commandResult->message,
          $commandStr
        );
        $browserResult = FALSE;
      }

      $this->polling($pollFile, array(
        $test->getPath(),
        $res
      ));

      if ($commandResult->continue === FALSE) {
        break;
      }
      $i++;
    }

    if ($pageConf['screenshots'] == 'test') {
      $imageName = 'afterTest.png';
      $this->captureEntirePageScreenshot($profile, $webDriver, $page, $imageName, $res);
      $this->polling($pollFile, $res);
    }

    $webDriver->quit();
    $matrix = new Matrix($page, $this->browser);
    $matrix->writeResult($res, $profile);

    unlink($pollFile);
    $this->cleanupTempFiles();
    return $res;
  }

  private function cleanupTempFiles() {
    if (file_exists(sys_get_temp_dir().'/chromeinstances')) {
      $this->delete(sys_get_temp_dir().'/chromeinstances');
    }
  }

  /**
   * @param $dir
   * @return bool
   */
  protected function delete($dir) {
    $files = array_diff(scandir($dir), array(
      '.',
      '..'
    ));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? $this->delete("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
  }


  private function polling($pollFile, $res) {
    file_put_contents($pollFile, json_encode($res));
  }


  private function captureEntirePageScreenshot($profile, $webDriver, $page, $imagename, &$res) {

    $srcImage = $this->getPath($profile) . "/" . $imagename;
    $res[] = $this->takeScreenshot($profile, $webDriver, $srcImage);

    $compareObj = new Compare();
    $compare = $compareObj->compare($profile, $imagename, $page->getPath(), $this->imageDir);
    return $compareObj->compareResult($compare, $res, $imagename);
  }

  private function takeScreenshot($profile, $webDriver, $srcImage) {

    if (strpos($profile['browser'], 'chrome') !== FALSE && $this->webdriverSetup->local === TRUE) {
      $javascript = new Javascript($webDriver);
      $javascript->invokeHtml2Canvas();
      $javascript->invokeNanoajax();
      sleep(2);
      $javascript->invokeTakeScreenshot($srcImage);
      sleep(2);

    } else {
      $screenCommand = new CaptureEntirePageScreenshot();
      $screenCommand->arg1 = $srcImage;
      $screenCommand->runWebDriver($webDriver);
      sleep(2);
    }

    return array(
      TRUE,
      'Take Screenshot ' . $srcImage,
      'captureEntirePageScreenshot'
    );
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

  protected function getProfileName($profile) {
    if (isset($profile['browser'])) {
      if (isset($profile['name'])) {
        $profileName = $profile['name'] . ' ' . $profile['browser'];
      } else {
        $profileName = $profile['browser'] . ' default';
      }
    }
    return $profileName;
  }


}
