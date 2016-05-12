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
use testonaut\Config;
use testonaut\Search\File;

class Ajax extends Base implements ProviderInterface {

  private $routing;
  private $response;
  protected $path = '';
  /**
   * @var null| Git
   */
  protected $git = NULL;

  public function connect() {
    $this->routing = new Routing();
    $this->response = array(
      'system' => $this->system()
    );

    $this->routing->route('.*/image/(.*)', function ($term) {
      $search = new File(Config::getInstance()->Path . '/index.db', 'files', Config::getInstance()->fileRoot);
      
      print(json_encode($search->search($term)));

    });
    
  }

}