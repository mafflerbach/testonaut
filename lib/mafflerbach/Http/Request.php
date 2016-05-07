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


namespace mafflerbach\Http;


class Request {
  public $request;
  public $server;

  public function __construct() {
    $this->request = $_REQUEST;
    $this->server = $_SERVER;
  }

  public function get() {
    return array(
      'request' => $_REQUEST,
      'cookie' => $_COOKIE,
      'session' => $_SESSION,
      'server' => $_SERVER,
      );
  }

}