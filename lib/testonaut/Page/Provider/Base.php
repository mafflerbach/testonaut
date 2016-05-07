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

class Base {

  public function system() {
    $system['baseUrl'] = $this->getBaseUrl();
    $system['requestUri'] = $this->getRequestUri();
    $system['globalconfig'] = $this->getConfig();

    return $system;
  }

  public function getBaseUrl() {
    $request = new Request();
    $basePath = str_replace('index.php', '', $request->server['PHP_SELF']);

    return $basePath;
  }

  public function getRequestUri() {
    $request = new Request();
    $basePath = str_replace('index.php', '', $request->server['PHP_SELF']);
    $requestUri = $request->server['REQUEST_URI'];

    if (array_key_exists('xml', $request->request)) {
      $requestUri = str_replace('?xml=true', '', $requestUri);
      $requestUri = str_replace('&xml=true', '', $requestUri);
    }

    $paramQuery = str_replace($basePath, '', $requestUri);

    return $paramQuery;
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


}