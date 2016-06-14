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
use testonaut\Settings\Saucelabs;

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

    $this->routing->route('.*/(.+)$', function ($profilename) {
      $request = new Request();
      $messageBody = "";
      $result = 'fail';

      if (!empty($request->post)) {
        if ($this->deleteProfile($profilename) !== FALSE) {
          $messageBody = "Delete Profile";
          $result = 'success';
        } else {
          $messageBody = "Can't delete Profile";
          $result = 'fail';
        }

        $message = array(
          'result' => $result,
          'message' => $messageBody,
          'messageTitle' => 'Delete'
        );

        print(json_encode($message));
        die;

      } else {
        $profile = new Profile();

        $profileList = $profile->getByName($profilename);
        $me = json_decode($profileList[0]['capabilities'], true);

        $profileList[0]['capabilities'] = $me;

        print(json_encode($profileList));
        die;
      }

    });

    $this->routing->route('', function () {

      $saucelabs = new Saucelabs();
      $saucelabs->getSupportedSettings();


      $request = new Request();
      if (!empty($request->post)) {
        $this->handelPostData($request);
      }

      $conf = $this->getConfig();
      $this->response['menu'] = $this->getMenu('', 'globalconfig');

      $profile = new Profile();
      $profileList = $profile->get();

      $emulator = new Emulator();
      $devices = $emulator->getDevices();

      $this->response['devices'] = $devices;
      $this->response['profiles'] = $profileList;

      $user = new \testonaut\User();
      $this->response['user'] = $user->getAll();

      $this->routing->response($this->response);
      $this->routing->render('globalconfig.xsl');
    });
  }

  protected function deleteProfile($profilename) {
    $profile = new Profile();
    return $profile->delete($profilename);
  }

  protected function handelPostData($request) {


    if ($request->post['action'] == 'savebase') {
      $this->saveConfigForm($request->post);
    }
    if ($request->post['action'] == 'saveprofile') {
      $this->saveProfile($request->post);
    }
    if ($request->post['action'] == 'savesaucelabsprofile') {
      $this->saveSaucelabsProfile($request->post);
    }
    if ($request->post['action'] == 'save_saucelabs') {
      $this->saveSauceLabs($request->post);
    }
  }


  private function saveSaucelabsProfile($request) {
    $this->saveProfile($request, FALSE);
  }

  private function saveSauceLabs($request) {
    $conf = $this->getConfig();

    $conf['saucelabs_username'] = $request['saucelabs_username'];
    $conf['access_key'] = $request['access_key'];
    $conf['saucelabs_seleniumAddress'] = $request['saucelabs_seleniumAddress'];
    if(isset($request['useSaucelabs'])) {
      $conf['useSaucelabs'] = 1;
    } else {
      $conf['useSaucelabs'] = 0;
    }

    $this->saveConfig($conf);
  }


  private function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  /**
   * @param $request
   */

  protected function saveProfile($request, $local = TRUE) {


    $profile = new Profile();
    $data = array();

    $browserSettings = explode('_', $request['browser']);
    $data['local'] = $local;


    if (count($browserSettings) > 1) {
      $data['name'] = $request['profileName'];
      $browser = $browserSettings[0];
      $data['os'] = $browserSettings[2];
      $data['version'] = $browserSettings[1];
    } else {
      $browser = $request['browser'];
      $data['os'] = $request['os'];
      $data['version'] = $request['version'];
      $data['name'] = $request['profileName'];
    }

    $data['browser'] = $browser;

    if ($data['version'] == 'default') {
      $data['version'] = 'ANY';
    }

    if (isset($request['version'])) {
      $data['version'] = $request['version'];
    }



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
        "--user-data-dir=" . sys_get_temp_dir() . '/chromeinstances/' . $this->generateRandomString()
      );

      if (isset($request['device']) && $request['device'] != '' && $request['width'] == '' && $request['height'] == '') {
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

    $data['driverOptions'] = $driverOptions;
    $data['arguments'] = $arguments;

    if ($capabilities != '') {
      $data['capabilities'] = json_encode($capabilities);
    } else {
      $data['capabilities'] = '';
    }

    var_dump($data);


    $profile->write($data);
  }

  protected function saveConfigForm($request) {
    $configuration = $this->getConfig();
    $address = $request['seleniumAddress'];
    $appPath = $request['appPath'];
    $ldapHostname = $request['ldapHostname'];
    $ldapBaseDn = $request['ldapBaseDn'];
    $ldapCn = $request['ldapCn'];
    $ldapPassword = $request['ldapPassword'];

    if (isset($request['useLdap'])) {
      $useLdap = true;
    } else {
      $useLdap = false;
    }

    $configuration['appPath'] = $appPath;
    $configuration['seleniumAddress'] = $address;
    $configuration['ldapHostname'] = $ldapHostname;
    $configuration['ldapBaseDn'] = $ldapBaseDn;
    $configuration['ldapCn'] = $ldapCn;
    $configuration['ldapPassword'] = $ldapPassword;
    $configuration['useLdap'] = $useLdap;


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
