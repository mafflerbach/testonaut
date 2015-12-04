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



namespace testonaut\Selenese;

class Locator {

  public $type;
  public $argument;
  public $by;

  public function __construct($locator) {
    $explode = explode('=', $locator, 2);
    if (count($explode) == 2) {
      $this->type = $explode[0];
      $this->argument = $explode[1];
    } else {
      $this->argument = $locator;
      if (substr($locator, 0, 9) == 'document.') {
        $this->type = 'dom';
      } elseif (substr($locator, 0, 2) == '//') {
        $this->type = 'xpath';
      } else {
        $this->type = 'identifier';
      }
    }
    // convert from selenese to webdriver
    switch ($this->type) {
      // todo: fix these exceptions if possible/needed
//            case 'identifier': // todo: this is possible with some song & dance in a common locator routine
//            case 'ui': // ha. haha. hahahahahaha. No. Good luck on whomever might tackle this...
//            case 'dom': // I don't think this one is possible
      case 'css':
        $this->by = \WebDriverBy::cssSelector($this->argument);
        break;

      case 'id':
        $this->by = \WebDriverBy::id($this->argument);
        break;

      case 'name':
        $this->by = \WebDriverBy::name($this->argument);
        break;

      case 'link':
        $this->by = \WebDriverBy::partialLinkText($this->argument);
        break;

      case 'xpath':
        $this->by = \WebDriverBy::xpath($this->argument);
        break;
      default:
          $this->by = \WebDriverBy::xpath($locator);
        break;
    }
  }

}
