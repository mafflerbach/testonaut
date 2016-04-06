<?php
/**
 *
 * GNU GENERAL PUBLIC LICENSE testonaut Copyright (C) 2016 Afflerbach
 * This program is free software: you can redistribute it and/or modify it under the terms 
 * of the GNU General Public License as published by the Free Software Foundation, 
 * either version 3 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU General Public License for more details.
 *
 */


namespace testonaut\Settings\Emulator;
class Devices {
  protected $devices = array();

  public function __construct() {
    $this->fetchDeviceJson();
  }

  public function getDevices() {
    return $this->devices;
  }

  protected function fetchDeviceJson() {
    $url = "https://src.chromium.org/blink/trunk/Source/devtools/front_end/emulated_devices/module.json";
    $module = file_get_contents($url);

    $moduleArray = json_decode($module, true);

    for($i = 0; $i < count($moduleArray['extensions']); $i++) {
      $this->devices[str_replace(' ', '_', $moduleArray['extensions'][$i]['device']['title'])] = $moduleArray['extensions'][$i]['device']['title'];
    }
  }


}