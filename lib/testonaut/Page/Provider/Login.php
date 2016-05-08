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

/**
 * Class Login
 * @package testonaut\Page\Provider
 */
class Login extends Base implements ProviderInterface {

  private $routing;
  private $response;

  public function connect() {
    $this->routing = new Routing();
    $this->response = array(
      'system' => $this->system()
    );

    $this->routing->route('/', function () {
      $request = new \mafflerbach\Http\Request();

      $this->response['form'] = $this->getForm();

      $data = $request->request;
      if (!empty($request->request) && isset($data['username']) && isset($data['password'])) {
        $user = new \testonaut\User();

        if ($user->validate($data['username'], $data['password'])) {
          $request->redirect('hello/');
        } else {

          $this->response['message'] = 'User not valid';
        }
      }

      $this->routing->response($this->response);
      $this->routing->render('login.xsl');
    });
  }

  private function getForm() {
    return array(
      'text' => array('label' => 'username'),
      'password' => array('label' => 'password'),
    );
  }

}