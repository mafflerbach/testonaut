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

use testonaut\Page\Breadcrumb;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use testonaut\Settings\Emulator;
use testonaut\Settings\Profile;

/**
 *
 *
 *
 * $seleniumAddress = 'http://selenium-hub.dim:4444';
 * $config = \testonaut\Config::getInstance();
 * $config->define('Path', dirname(dirname(__FILE__)));
 * $config->define('wikiPath', dirname(dirname(__FILE__)) . '/root');
 * $config->define('imageRoot', dirname(dirname(__FILE__)) . '/images');
 * $config->define('result', dirname(dirname(__FILE__)) . '/result');
 * $config->define('seleniumHub', $seleniumAddress.'/wd/hub');
 * $config->define('seleniumConsole', $seleniumAddress.'/grid/console');
 * $config->define('appPath', '');
 * $config->define('Cache', FALSE);
 * $config->define('seleniumAddress', $seleniumAddress);
 *
 *
 */
class Globalconfig implements ControllerProviderInterface {

  public function connect(Application $app) {
    $edit = $app['controllers_factory'];
    $edit->get('/', function (Request $request) use ($app) {
      $conf = $this->getConfig();

      $profile = new Profile();
      $profileList = $profile->get();

      $emulator = new Emulator();
      $devices = $emulator->getDevices();

      $app['request'] = array(
        'baseUrl' => $request->getBaseUrl(),
        'mode' => 'edit',
        'settings' => $conf,
        'themes' => $this->getThemes(),
        'profiles' => $profileList,
        'devices' => $devices
      );

      var_dump($profileList);

      return $app['twig']->render('globalconfig.twig');
    });

    $edit->post('/', function (Request $request) use ($app) {

      if ($request->request->get('save') == 'profile') {
        $this->saveProfile($request);
      } else {
        $this->saveConfigForm($request);
      }

      return $app->redirect($request->getBaseUrl() . '/globalconfig/');
    });
    return $edit;
  }

  /**
   * @param $request
   *
   *
   *
   * $browser = $data['browser'];
   * $name = $data['name'];
   * $driverOptions = json_encode($data['driverOption']);
   * $arguments = json_encode($data['arguments']);
   * $capabilities = json_encode($data['capabilities']);
   */

  protected function saveProfile($request) {
    $profile = new Profile();
    $data = array();

    $browser = $request->request->get('browsers');
    $name = $request->request->get('profileName');
    $driverOptions = '';
    if ($request->request->get('width') != '' && $request->request->get('height') != '') {
      $driverOptions = json_encode(array('dimensions' => array('width' => $request->request->get('width'), "height" => $request->request->get('height'))));
    }
    if ($browser == 'chrome') {
      $capabilities['arguments'] = array(
        "--disable-web-security",
        "--user-data-dir=C:\\Users\\maren\\AppData\\Local\\Temp"
      );

      if ($request->request->get('device') != '' && $request->request->get('width') == '' && $request->request->get('height') != '') {
        $capabilities['experimental'] = array('mobileEmulation' => array(
          "deviceName" =>$request->request->get('device')
        ));
      }
    } else {
      $capabilities = '';
    }


    $arguments = '';
    $data['browser'] = $browser;
    $data['name'] = $name;
    $data['driverOptions'] = $driverOptions;
    $data['arguments'] =  '';

    if ($capabilities != '') {
      $data['capabilities'] = json_encode($capabilities);
    } else {
      $data['capabilities'] = '';
    }

    $profile->write($data);

  }

  protected function saveConfigForm($request) {
    $address = $request->request->get('seleniumAddress');
    $cache = $request->request->get('cache');
    $appPath = $request->request->get('appPath');
    $theme = $request->request->get('theme');

    if ($cache != null) {
      $cache = true;
    } else {
      $cache = false;
    }

    $configuration = array(
      'appPath' => $appPath,
      'cache' => $cache,
      'theme' => $theme,
      'seleniumAddress' => $address);

    $this->saveConfig($configuration);
  }

  protected function saveConfig($array) {
    $config = \testonaut\Config::getInstance()->Path . '/config';
    $this->writeToFile($config, json_encode($array));
  }

  public function getConfig() {

    $config = \testonaut\Config::getInstance()->Path . '/config';
    if (file_exists($config)) {
      $configuration = json_decode(file_get_contents($config), true);
    } else {

      $configuration = array(
        'appPath' => '',
        'cache' => '',
        'theme' => 'bootstrap',
        'seleniumAddress' => '');
      $this->writeToFile($config, json_encode($configuration));
    };
    return $configuration;
  }

  protected function writeToFile($path, $content) {
    file_put_contents($path, $content);
  }

  protected function getThemes() {
    $themePath = \testonaut\Config::getInstance()->Path .'/web/css/themes';
    $themes = array();
    foreach (new \DirectoryIterator($themePath) as $fileInfo) {
      if ($fileInfo->isDot()) {
        continue;
      }
      $themes[] = str_replace('.css', '', $fileInfo->getFilename());
    }
    return $themes;
  }

}
