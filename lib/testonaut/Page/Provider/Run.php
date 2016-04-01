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

use testonaut\Capabilities;
use testonaut\Matrix;
use testonaut\Page;
use testonaut\Page\Breadcrumb;
use testonaut\Selenium\Api;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use testonaut\Selenese\Runner;
use testonaut\Settings\Profile;

class Run implements ControllerProviderInterface {

  private $basePath;

  /**
   * @var \testonaut\Page $page
   */
  private  $page;
  private $imagePath;
  private $dirArray = array();
  private $path;
  private $browser = '';

  public function connect(Application $app) {

    $edit = $app['controllers_factory'];
    $edit->get('/', function (Request $request, $path) use ($app) {

      $this->path = $path;

      $this->page = new \testonaut\Page($path);
      $this->basePath = $this->page->transCodePath();
      $this->imagePath = $this->page->getImagePath();

      $capabilities = array();

      $this->browser = $request->query->get('browser');
      if ($this->browser == '') {
        $this->browser = 'all';
      }

      $this->version = $request->query->get('version');
      if ($this->version == '') {
        $this->version = '';
      }

      $this->platform = $request->query->get('platform');
      if ($this->platform == '') {
        $this->platform = '';
      }

      $this->profile = $request->query->get('profile');
      if ($this->profile == '') {
        $this->profile = '';
      }

      if ($request->query->get('suite') == 'true') {
        $result = $this->runSuite($this->page);
      } else {
        $result = $this->run($this->page);
      }

      if ($this->profile != '') {
        $profiles = new Profile();
        $profile = $profiles->getByName($this->profile);
        $capabilities = $profile[0];
        $capabilities['browser'] = $profile[0]['name'].'_'.$profile[0]['browser'];
        $capabilities['version'] = $this->version;
        $capabilities['platform'] = $this->platform;
      } else {
        $capabilities['browser'] = $this->browser;
        $capabilities['version'] = $this->version;
        $capabilities['platform'] = $this->platform;
      }

      $app['request'] = array(
        'path' => $path,
        'baseUrl' => $request->getBaseUrl(),
        'host' => $request->getHost(),
        'mode' => 'edit'
      );
      $crumb = new Breadcrumb($path);
      $app['crumb'] = $crumb->getBreadcrumb();
      $app['result'] = $result;

      return $app['twig']->render('run.twig');
    });

    return $edit;
  }



  /**
   * @param $content
   */
  protected function writeResultFile($content, $capabilities) {
    $matrix = new Matrix($this->page, $this->browser);
    $matrix->writeResult($content, $capabilities);
  }

  /**
   * @param $path
   * @return array
   */
  protected function runSuite($path) {

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
   * @param $path
   * @return array
   */
  protected function run($path) {

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
      $result = $runner->run();

      $browserResult = TRUE;
      for($i = 0; $i < count($result); $i++) {
        for($k = 0; $k < count($result[$i]); $k++) {
          if($result[$i][$k][0] == false) {
            $browserResult = FALSE;
          }
        }
      }

      return array(
        'run' => $result,
        'browserResult' => $browserResult,
        'path' => $tests[0]->getPath()
      );

    } catch (\Exception $e) {
      return array(array(
          'run' => array(array(FALSE, $e->getMessage(), "open connection")),
          'browserResult' => FALSE,
          'path' => $tests[0]->getPath()
      ));
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
      $profile['version'] = $this->version;
      $profile['platform'] = $this->platform;

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
