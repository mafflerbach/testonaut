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


namespace mafflerbach;


use mafflerbach\Http\Request;

class Application {

  private $request;
  private $pattern;
  private $provider;

  public function createRequest() {
    $this->request = new Request();
  }

  public function getRequest() : Request {
    return new Request();
  }


  public function getResponse() {

  }

  public function get($pattern, $to = null) {
    var_dump('sdfasdf');
    //return $this['controllers']->get($pattern, $to);
  }

  public function setPattern($pattern) {
    $this->pattern = $pattern;
  }

  public function setProvider($provider) {
    $this->provider = $provider;
  }

  public function controller() {
    $server = $this->getRequest()->server;

    $basePath = str_replace('index.php', '', $server['PHP_SELF']);
    $paramQuery = str_replace($basePath,'', $server['REQUEST_URI']);
    $param = explode('/', $paramQuery);

    var_dump($param);
    $mee[] =  $this->getRequest();
    $mee[] =  $this->pattern;

    return new Controller($mee);
  }


}