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

use mafflerbach\Page\ProviderInterface;
use mafflerbach\Xml\Util;
use testonaut\Config;
use testonaut\User;

class Routing {
  private static $routes = array();
  private $provider = array();
  private $before = array();
  private $after = array();
  private $content = '';
  private $activProvider = '';
  public $response = array();

  private function __clone() {
  }

  public function route($pattern, $callback) {
    $pattern = '/' . str_replace('/', '\/', $pattern) . '/';
    self::$routes[$pattern] = $callback;
  }

  public function execute() {

    $this->attachBeforeProvider();
    $this->attachSiteProvider();
    $this->attachAfterProvider();

  }

  private function attachSiteProvider() {

    $basePath = str_replace('index.php', '', $_SERVER['PHP_SELF']);
    $requestUri = $_SERVER['REQUEST_URI'];

    if (isset($_REQUEST['xml'])) {
      $requestUri = str_replace('?xml=true', '', $requestUri);
      $requestUri = str_replace('&xml=true', '', $requestUri);
    }
    $paramQuery = str_replace($basePath, '', $requestUri);

    foreach ($this->provider as $route => $provider) {
      /**
       * @var $provider ProviderInterface
       */

      $mee = str_replace($route, '/', $paramQuery);
      $routepattern = '/' . str_replace('/', '\/', $route) . '/';

      if (preg_match($routepattern, $mee, $result)) {

        $provider->connect();

        foreach (self::$routes as $pattern => $callback) {

          if (preg_match($pattern, $mee, $params)) {
            $this->activProvider = get_class($provider);
            array_shift($params);
            call_user_func_array($callback, array_values($params));
          }
        }
        break;
      }
    }
  }

  protected function imageExists($webpath) {
    $path = explode('/', $webpath);

    array_shift($path);
    array_shift($path);
    $imagePath = Config::getInstance()->Path . '/'. implode('/',$path);

    return file_exists($imagePath);
  }


  public function push($route, ProviderInterface $provider) {
    $this->provider[$route] = $provider;
  }

  public function response(array $response) {
    $user = new User();

    $this->response = $response;

    $this->response['system']['login']['status'] = $user->checkUser();
    $this->response['system']['login']['group'] = $user->isAdmin();

    $dom = new Util('1.0', 'utf-8');
    $dom->formatOutput = true;
    $dom->nodeCreate($this->response, null, false, 'data');

    $this->content = $dom->saveXML();
  }

  public function render($file) {

    $requestUri = $_SERVER['REQUEST_URI'];

    $this->cacheHeader();

    preg_match('/.*.(png|gif|jpg)$/', $requestUri, $extension);
    if (count($extension) >= 1 && !$this->imageExists($requestUri)) {
      header("HTTP/1.0 404 Not Found");
      header('Content-Type: image/'.$extension[1]);
      die;
    }

    putenv('XML_CATALOG_FILES=' . Config::getInstance()->Path . '/dtd/catalog.xml');

    $xslDoc = new \DOMDocument();
    $content = $this->content;
    
    $templateDir = Config::getInstance()->templates;
    $xslDoc->load($templateDir . $file);
    $xmlDoc = new \DOMDocument();


    $xmlDoc->loadXML($content);

    $proc = new \XSLTProcessor();
    $proc->importStylesheet($xslDoc);


    if (isset($_REQUEST['xml'])) {
      header('Content-Type: text/xml');
      echo $this->content;
      die;
    } else {

      header('Content-Type: text/html');
      print($proc->transformToXml($xmlDoc));
      die;
    }
  }

  public function before(ProviderInterface $provider) {
    $this->before[] = $provider;
  }

  private function attachBeforeProvider() {
    foreach ($this->before as $provider) {
      /**
       * @var $provider ProviderInterface
       */
      $provider->connect();
    }
  }

  private function attachAfterProvider() {
    foreach ($this->after as $provider) {
      /**
       * @var $provider ProviderInterface
       */

      if (method_exists($provider, 'observe'))
      {
        $provider->obj($this);
        $provider->connect();
      } else {
        $provider->connect();
      }
    }
  }

  public function after(ProviderInterface $provider) {
    $this->after[] = $provider;
  }

  protected function cacheHeader() {
    if (Config::getInstance()->debug) {
      $ts = gmdate("D, d M Y H:i:s") . " GMT";
      header("Expires: $ts");
      header("Last-Modified: $ts");
      header("Pragma: no-cache");
      header("Cache-Control: no-cache, must-revalidate");
    } else {
      $seconds_to_cache = 36000;
      $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
      header("Expires: $ts");
      header("Pragma: cache");
      header("Cache-Control: max-age=$seconds_to_cache");
    }
  }




}