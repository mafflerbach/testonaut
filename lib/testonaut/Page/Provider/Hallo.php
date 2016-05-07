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

use mafflerbach\Page;
use mafflerbach\Routing;

class Hallo extends Base implements Page\ProviderInterface {
  private $route;
  /**
   * @var Routing $routing
   */
  private $routing;
  private $response;

  public function connect() {

    $this->routing = new Routing();
    $this->response = array(
      'system' => $this->system()
    );

    $this->routing->route('/', function(){
      $this->response['content'] = array('hello' => 'world');

      $this->routing->response($this->response);
      $this->routing->render('test.xsl');
    });


    $this->routing->route('/(\w+)', function($category){

      $this->response['content'] = array('hello' => $category);

      $this->routing->response($this->response);
      $this->routing->render('test.xsl');
    });

    $this->routing->route('/(\w+)/(\w+)', function($category, $id){
      $this->response['content'][] = array('hello' => $category);
      $this->response['content'][] = array('hello2' => $id);
      $this->routing->response($this->response);
      $this->routing->render('test.xsl');
    });
    
  }
}