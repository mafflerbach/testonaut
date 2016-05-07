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


namespace testonaut;


class State {

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

}