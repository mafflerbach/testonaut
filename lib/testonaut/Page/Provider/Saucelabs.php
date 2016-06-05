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


use mafflerbach\Page\ProviderInterface;
use mafflerbach\Routing;

class Saucelabs extends Base implements ProviderInterface {

  private $routing;

  public function connect() {
    $this->routing = new Routing();
    $this->response = array(
      'system' => $this->system()
    );

    $this->routing->route('', function () {
      $request = new \mafflerbach\Http\Request();

      $labs = new \testonaut\Settings\Saucelabs();
      $this->response['settings'] = $labs->getSupportedSettings();
      $this->response['menu'] = $this->getMenu('', '');
      
      if (!empty($request->post)) {
        print_r($request->post); die;

      }



      $this->routing->response($this->response);
      $this->routing->render('saucelabs.xsl');
    });
  }
}