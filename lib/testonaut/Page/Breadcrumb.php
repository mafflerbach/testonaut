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


namespace testonaut\Page;

class Breadcrumb {

  private $path;

  public function __construct($path) {

    $this->path = $path;
  }

  public function getBreadcrumb() {

    $crumbs = explode(".", $this->path);
    $foo = array();
    $b = '';
    $i = 0;
    foreach ($crumbs as $crumb) {
      if ($b != $crumb && $b != '') {
        $b .= '.';
      }
      $foo[$crumb] = $b .= $crumb;
      $i++;
    }

    return $foo;
  }
}