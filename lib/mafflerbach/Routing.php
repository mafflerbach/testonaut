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
use testonaut\Config;

class Routing {
  private static $routes = array();
  private $provider = array();
  private $content = '';

  private function __clone() {
  }

  public function route($pattern, $callback) {
    $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
    self::$routes[$pattern] = $callback;
  }

  public function execute() {
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
      $provider->connect();
      $mee = str_replace($route, '/', $paramQuery);

      foreach (self::$routes as $pattern => $callback) {
        if (preg_match($pattern, $mee, $params)) {
          array_shift($params);
          return call_user_func_array($callback, array_values($params));
        }
      }
    }
  }

  public function push($route, ProviderInterface $provider) {
    $this->provider[$route] = $provider;
  }

  public function response(array $response) {

    $xml_data = new \SimpleXMLElement('<?xml version="1.0"?><data></data>');
    $this->array_to_xml($response, $xml_data);

    $dom = new \DOMDocument();
    $dom->formatOutput = true;
    $dom->loadXML($xml_data->saveXML());

    $this->content = $dom->saveXML();
  }

  public function render($file) {

    $xslDoc = new \DOMDocument();

    $templateDir = Config::getInstance()->templates;

    $xslDoc->load($templateDir . $file);

    $xmlDoc = new \DOMDocument();
    $xmlDoc->loadXML($this->content);


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


  private function array_to_xml($data, &$xml_data) {
    foreach ($data as $key => $value) {
      if (is_array($value)) {
        if (is_numeric($key)) {
          $key = 'item' . $key;
        }
        $subnode = $xml_data->addChild($key);
        $this->array_to_xml($value, $subnode);
      } else {
        $xml_data->addChild("$key", htmlspecialchars("$value"));
      }
    }
  }


}