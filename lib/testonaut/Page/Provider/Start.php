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
use testonaut\Generate;


class Start extends Base implements ProviderInterface {

  private $routing;
  private $response;

  public function connect() {
    $this->routing = new Routing();
    $this->response = array(
      'system' => $this->system()
    );


    $this->routing->route('(.*)$', function ($path) {
      $path = urldecode($path);

      $this->response['page'] = $this->getContent($path);
      $this->response['menu'] = $this->getMenu($path);

      $this->routing->response($this->response);
      $this->routing->render('page.xsl');
    });


    $this->routing->route('', function () {

      $this->response['page'] = $this->getContent('');
      $this->response['menu'] = $this->getMenu('');

      $this->routing->response($this->response);
      $this->routing->render('page.xsl');
    });
  }

  private function getContent($path) {
    $page = new \testonaut\Page($path);

    return array(
      'content' => html_entity_decode($page->getCompiledPage()),
      'config' => $page->config()
    );
  }
}
