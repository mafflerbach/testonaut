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


use mafflerbach\Application;
use mafflerbach\Http\Request;
use mafflerbach\Page;
use mafflerbach\Routing;

class Test implements Page\ProviderInterface{
  public function connect() {
    $routing = new Routing();

    $routing->route('/', function(){
      print 'ds345';
    });

    $routing->route('test/(\w+)', function($category){
      print '543qw';
    });

    $routing->route('test/(\w+)/(\w+)', function($category, $id){
      print '1234';
    });

  }
}