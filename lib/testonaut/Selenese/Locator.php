<?php

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
