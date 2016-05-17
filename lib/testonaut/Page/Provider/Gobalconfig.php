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

use mafflerbach\Http\Request;
use mafflerbach\Page\ProviderInterface;
use mafflerbach\Routing;
use testonaut\Generate;
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
class Globalconfig extends Base implements ProviderInterface {


  public function connect() {
    $this->routing = new Routing();
    $this->response = array(
      'system' => $this->system()
    );


    $this->routing->route('', function () {
      $request = new Request();
      if (!empty($request->post)) {
        $this->handelPostData($request);
      }


      $conf = $this->getConfig();
      $this->response['menu'] = $this->getMenu('');

      $profile = new Profile();
      $profileList = $profile->get();

      $emulator = new Emulator();
      $devices = $emulator->getDevices();

      $this->response['devices'] = $devices;
      $this->response['profiles'] = $profileList;

      $this->routing->response($this->response);
      $this->routing->render('globalconfig.xsl');
    });
  }

  protected function handelPostData($request) {
    if ($request->post['action'] == 'savebase') {
      $this->saveConfigForm($request->post);
    }
    if ($request->post['action'] == 'saveprofile') {
      $this->saveProfile($request->post);
    }
  }


  /*  public function connect(Application $app) {
      $edit = $app['controllers_factory'];
      $edit->get('/', function (Request $request) use ($app) {
        if (!isset($_SESSION['testonaut']['userId'])) {
          return $app->redirect($request->getBaseUrl() . '/login/');
        }

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

        return $app['twig']->render('globalconfig.twig');
      });

      $edit->get('/deleteProfile/{browserProfile}', function (Request $request, $browserProfile) use ($app) {

        $app['request'] = array(
          'baseUrl' => $request->getBaseUrl(),
          'mode' => 'edit',
          'profileName' => $browserProfile
        );

        return $app['twig']->render('globalconfig.twig');
      });

      $edit->post('/deleteProfile/{browserProfile}', function (Request $request, $browserProfile) use ($app) {
        $profile = new Profile();
        $profile->delete($browserProfile);

        $app['request'] = array(
          'baseUrl' => $request->getBaseUrl(),
          'mode' => 'edit',
          'message' => 'deleted'
        );
        return $app['twig']->render('globalconfig.twig');
      });

      $edit->get('/editProfile/{browserProfile}', function (Request $request, $browserProfile) use ($app) {

        $profile = new Profile();
        $profileList = $profile->getByName($browserProfile);

        $data = array(
          'browser' => $profileList[0]['browser'],
          'name' => $profileList[0]['name'],
          'arguments' => ($profileList[0]['arguments'] == '') ? '' : json_decode($profileList[0]['arguments'], true),
          'driverOptions' => ($profileList[0]['driverOptions'] == '') ? '' : json_decode($profileList[0]['driverOptions'], true),
          'capabilities' => ($profileList[0]['capabilities'] == '') ? '' : json_decode($profileList[0]['capabilities'], true),
        );

        return $app->json($data);
      });

      $edit->post('/deleteProfile/{browserProfile}', function (Request $request, $browserProfile) use ($app) {
        $profile = new Profile();
        $profile->delete($browserProfile);

        $app['request'] = array(
          'baseUrl' => $request->getBaseUrl(),
          'mode' => 'edit',
          'message' => 'deleted'
        );
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
  */
  /**
   * @param $request
   */

  protected function saveProfile($request) {
    $profile = new Profile();
    $data = array();

    $browser = '';
    if(strpos($request['browser'], 'chrome') !== FALSE) {
      $browser = 'chrome';
    }

    var_dump($request['browser']);
    if(strpos($request['browser'], 'firefox') !== FALSE) {
      $browser = 'firefox';
    }
    if(strpos($request['browser'], 'explorer ') !== FALSE) {
      $browser = 'internet explorer';
    }

    $name = $request['profileName'];
    $driverOptions = '';
    if ($request['width'] != '' && $request['height'] != '') {
      $driverOptions = json_encode(array(
        'dimensions' => array(
          'width' => $request['width'],
          "height" => $request['height']
        )
      ));
    }

    //@TODO add platform and version
    if ($browser == 'chrome') {
      $capabilities['arguments'] = array(
        "--disable-web-security",
        "--user-data-dir=" . sys_get_temp_dir()
      );

      if ($request['device'] != '' && $request['width'] == '' && $request['height'] == '') {
        $capabilities['experimental'] = array(
          'mobileEmulation' => array(
            "deviceName" => $request['device']
          )
        );
      }
    } else {
      $capabilities = '';
    }

    if ($browser == 'firefox') {
      $arguments = json_encode(array(
        'security.fileuri.strict_origin_policy' => false,
        'network.http.referer.XOriginPolicy' => 1
      ));
    } else {
      $arguments = '';
    }

    $data['browser'] = $browser;
    $data['name'] = $name;
    $data['driverOptions'] = $driverOptions;
    $data['arguments'] = $arguments;

    if ($capabilities != '') {
      $data['capabilities'] = json_encode($capabilities);
    } else {
      $data['capabilities'] = '';
    }

    $profile->write($data);
  }

  protected function saveConfigForm($request) {
    $address = $request['seleniumAddress'];
   // $cache = $request['cache'];
    $appPath = $request['appPath'];
   // $theme = $request['theme'];
    $ldapHostname = $request['ldapHostname'];
    $ldapBaseDn = $request['ldapBaseDn'];
    $ldapCn = $request['ldapCn'];
    $ldapPassword = $request['ldapPassword'];

    if (isset($request['useLdap'])) {
      $useLdap = true;
    } else {
      $useLdap = false;
    }

    $configuration = array(
      'appPath' => $appPath,
    //  'cache' => $cache,
   //   'theme' => $theme,
      'seleniumAddress' => $address,
      'ldapHostname' => $ldapHostname,
      'ldapBaseDn' => $ldapBaseDn,
      'ldapCn' => $ldapCn,
      'ldapPassword' => $ldapPassword,
      'useLdap' => $useLdap
    );

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
        'seleniumAddress' => '',
        'ldapHostname' => '',
        'ldapBaseDn' => '',
        'ldapCn' => '',
        'ldapPassword' => '',
        'useLdap' => ''
      );

      $this->writeToFile($config, json_encode($configuration));
    };
    return $configuration;
  }

  protected function writeToFile($path, $content) {
    file_put_contents($path, $content);
  }

  protected function getThemes() {
    $themePath = \testonaut\Config::getInstance()->Path . '/web/css/themes';
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
