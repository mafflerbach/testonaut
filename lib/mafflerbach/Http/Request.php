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
  public $files;

  public function __construct() {
    $this->request = $_REQUEST;
    $this->server = $_SERVER;
    $this->files = $_FILES;
  }

  public function get() {
    return array(
      'request' => $_REQUEST,
      'cookie' => $_COOKIE,
      'session' => $_SESSION,
      'server' => $_SERVER,
    );
  }

  public function getSelf() {
    return str_replace('index.php', '', $_SERVER['PHP_SELF']);
  }

  public function getBasePath() {
    $requestUri = $_SERVER['REQUEST_URI'];

    if (isset($_REQUEST['xml'])) {
      $requestUri = str_replace('?xml=true', '', $requestUri);
      $requestUri = str_replace('&xml=true', '', $requestUri);
    }

    return $requestUri;
  }

  public function getPath () {
    $requestUri = str_replace('index.php', '', $_SERVER['PHP_SELF']);
    $requestUri = str_replace($requestUri, '', $_SERVER['REQUEST_URI']);

    return $requestUri.'/';
  }

  public function redirect($path) {
    $requestUri = str_replace('index.php', '', $_SERVER['PHP_SELF']);
    $url = $requestUri . $path;
    $from = $this->removeDebug($_SERVER['REQUEST_URI']);
    if ($from != $url) {
      header('Location: ' . $url);
      die;
    }
  }

  private function removeDebug($requestUri) {
    if (isset($_REQUEST['xml'])) {
      $requestUri = str_replace('?xml=true', '', $requestUri);
      $requestUri = str_replace('&xml=true', '', $requestUri);
      $requestUri .= '/';
      $this->debug = true;
    }
    return $requestUri;
  }

}