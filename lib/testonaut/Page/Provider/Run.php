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

namespace testonaut\Page\Provider;
ini_set('max_execution_time', 0);

use mafflerbach\Page\ProviderInterface;
use mafflerbach\Routing;
use testonaut\Matrix;
use testonaut\Page;
use testonaut\Selenese\Runner;
use testonaut\Settings\Browser;
use testonaut\Settings\Profile;

class Run extends Base implements ProviderInterface {

  private $basePath;

  /**
   * @var \testonaut\Page $page
   */
  private $page;
  private $imagePath;
  private $dirArray = array();
  private $path;
  private $browser = '';
  private $profile = '';

  public function connect() {
    $this->routing = new Routing();
    $this->response = array(
      'system' => $this->system()
    );

    $this->routing->route('.*/(.*)/(.*)/(.*)/(.*)', function ($path, $browser, $version, $platform) {

      $this->path = urldecode($path);

      $this->page = new \testonaut\Page($path);
      $this->basePath = $this->page->transCodePath();
      $this->imagePath = $this->page->getImagePath();

      $this->browser = $browser;
      $this->version = $version;
      $this->platform = $platform;

      $conf = $this->page->config();

      if ($conf['type'] == 'suite') {
        $result = $this->runSuite($this->page);
      } else {
        $result = $this->run($this->page);
      }

      $this->response['result'] = $result;

      $this->routing->response($this->response);
      $this->routing->render('run.xsl');
    });

    $this->routing->route('.*/(.*)/all', function ($path) {

      $this->browser = 'all';

      $this->page = new \testonaut\Page($path);
      $conf = $this->page->config();

      if ($conf['type'] == 'suite') {
        $result = $this->runSuite($this->page);
      } else {
        $result = $this->run($this->page);
      }

      $this->response['result'] = $result;
      $this->routing->response($this->response);
      $this->routing->render('run.xsl');
    });

    $this->routing->route('.*/(.*)/(.*)', function ($path, $profile) {
      $this->path = urldecode($path);

      $this->profile = $profile;
      $this->page = new \testonaut\Page($path);
      $conf = $this->page->config();

      if ($conf['type'] == 'suite') {
        $result = $this->runSuite($this->page);
      } else {
        $result = $this->run($this->page);
      }

      $this->response['result'] = $result;
      $this->routing->response($this->response);
      $this->routing->render('run.xsl');
    });
  }

  /**
   * @param $content
   */
  protected function writeResultFile($content, $capabilities) {
    $matrix = new Matrix($this->page, $this->browser);
    $matrix->writeResult($content, $capabilities);
  }

  /**
   * @param Page $path
   * @return array
   */
  protected function runSuite(Page $path) {

    $testCollect = array();

    $content = file_get_contents($path->transCodePath() . '/content');
    if (strpos($content, '<table') !== FALSE) {
      $this->dirArray[] = $path;
    }
    $this->collect($path->transCodePath());

    for ($i = 0; $i < count($this->dirArray); $i++) {
      $testCollect[] = $this->dirArray[$i];
    }

    $result = $this->_run($testCollect);

    return $result;
  }

  /**
   * @param Page $path
   * @return array
   */
  protected function run(Page $path) {

    $testCollect[] = $path;
    return $this->_run($testCollect);
  }

  /**
   * @return int
   */
  protected function screenshotSettings() {

    $conf = $this->page->config();
    switch ($conf['screenshots']) {
      case 'step';
        return 2;
        break;
      case 'test';
        return 1;
        break;
      case 'none';
        return 0;
        break;
      default:
        return 0;
        break;
    }
  }

  /**
   * @param $capabilities
   * @return string
   */
  protected function baseUrlSettings($capabilities) {
    $conf = $this->page->config();
    if (isset($conf['browser']['active']) && ($conf['type'] == 'suite' || $conf['type'] == 'project')) {
      if (in_array($capabilities->getBrowserName(), $conf['browser']['active'])) {
        return $conf['browser']['urls'][$capabilities->getBrowserName()];
      }
    } else {
      return '';
    }
  }

  /**
   * @param array $tests
   * @return array
   */
  private function _run(array $tests) {
    try {
      $profile = new Profile();

      if ($this->profile == '') {
        $profiles = $this->getCapabilities();
      } else {
        $profiles = $profile->getByName($this->profile);
      }

      $runner = new Runner($profiles, $this->page);
      $result = $runner->run($tests);

      $browserResult = TRUE;
      for ($i = 0; $i < count($result); $i++) {
        for ($k = 0; $k < count($result[$i]['result']); $k++) {
          if ($result[$i]['result'][$k][0] == false) {
            $browserResult = FALSE;
          }
        }
        $result[$i]['browserResult'] = $browserResult;
      }

      return $result;


    } catch (\Exception $e) {
      return array(
        array(
          'run' => array(
            array(
              FALSE,
              $e->getMessage(),
              "open connection"
            )
          ),
          'browserResult' => FALSE,
          'path' => $tests[0]->getPath()
        )
      );
    }
  }

  /**
   * @param $outerDir
   * @param array $tests
   * @return array
   */
  protected function collect($outerDir, $tests = array()) {

    $dirs = array_diff(scandir($outerDir), Array(
      ".",
      ".."
    ));

    $dir_array = Array();
    foreach ($dirs as $d) {
      if (is_dir($outerDir . "/" . $d)) {
        if (file_exists($outerDir . "/" . $d . '/content')) {
          $content = file_get_contents($outerDir . "/" . $d . '/content');

          if (strpos($content, '<table') !== FALSE) {
            $wikipath = \testonaut\Config::getInstance()->wikiPath . '/';
            $path = str_replace($wikipath, '', $outerDir . "/" . $d);
            $path = str_replace('/', '.', $path);

            $page = new Page($path);
            $this->dirArray[] = $page;
          }
        }
        $dir_array[$d] = $this->collect($outerDir . "/" . $d);
      } else {
        $dir_array[$d] = $d;
      }
    }

    return $dir_array;
  }

  /**
   * @return array
   */
  private function getCapabilities() {
    $profile = array();


    if ($this->browser == 'all') {

      $profileObj = new Profile();
      $list = $profileObj->get();
      $list = $list['all'];

      $capabilities = array();

      for ($i = 0; $i < count($list); $i++) {
        if (isset($list[$i]['browserName'])) {
          $browserName = $this->normalizeBrowserName($list[$i]['browserName']);
        } else {
          $browserName = $this->normalizeBrowserName($list[$i]['browser']);
          $profile['capabilities'] = $list[$i]['capabilities'];
          $profile['arguments'] = $list[$i]['arguments'];
          $profile['driverOptions'] = $list[$i]['driverOptions'];
        }
        $profile['browser'] = $this->normalizeBrowserName($browserName);


        if (isset($list[$i]['version'])) {
          $profile['version'] = $list[$i]['version'];
        }

        if (isset($list[$i]['platform'])) {
          $profile['platform'] = $list[$i]['platform'];
        }
        $capabilities[] = $profile;
      }

    } else {
      $profile['browser'] = $this->normalizeBrowserName($this->browser);
      if ($this->version != 'default') {
        $profile['version'] = $this->version;
      }

      if ($this->platform != 'default') {
        $profile['platform'] = $this->platform;
      } else {
        $profile['platform'] = 'ANY';
      }

      $profile['name'] = 'default';

      $capabilities[] = $profile;

    }
    return $capabilities;
  }

  /**
   * @param $browserString
   * @return mixed|string
   */
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
