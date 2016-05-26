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


namespace testonaut\Security;


use mafflerbach\Http\Request;
use mafflerbach\Page\ProviderInterface;
use testonaut\User;

class Provider implements ProviderInterface {
  private $rules = array();

  public function connect() {
    $this->checkUserSession();
  }

  public function setFirewall(array $rules) {
    $this->rules = $rules;
  }

  private function checkUserSession() {
    $user = new User();
    $request = new Request();
    $explode = explode('/', $request->getPath());
    if (!empty($this->rules) && in_array($explode[0] . '/', $this->rules['private'])) {
      if (!$user->checkUser()) {
        $request = new Request();
        $request->redirect('login/');
      }
    }
  }
}