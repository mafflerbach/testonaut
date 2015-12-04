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

use testonaut\Selenese;

abstract class Command {

  public $arg1;
  public $arg2;

  /**
   * @param \WebDriver $session
   * @return CommandResult
   */
  abstract public function runWebDriver(\WebDriver $session);

  /**
   * Utility function to fetch an element of throw an error
   *
   * @param \WebDriver $session
   * @param string $locator
   * @throws \Exception
   * @throws \NoSuchElementException
   * @return \WebDriverElement
   */
  protected function getElement(\WebDriver $session, $locator) {
    try {
      $locatorObj = new Locator($locator);
      $element = $session->findElement($locatorObj->by);
    } catch (\NoSuchElementException $e) {
      $element = null;
    }
    if ($element === null) {
      throw new \NoSuchElementException($locator);
    }
    return $element;
  }

  // these are all mostly simliar
  protected function assert($valueis, $pattern) {
    $patternobj = new Pattern($pattern);
    
    if (strpos($pattern, '${') !== FALSE) {
        $pattern = \testonaut\Utils\Variablestorage::getInstance()->$pattern;
    }
    
    $matched = $patternobj->match($valueis);
    return new CommandResult($matched, $matched, $matched ? 'Matched' : 'Did not match ' . $matched);
  }

  protected function assertNot($valueis, $pattern) {
    $patternobj = new Pattern($pattern);
    
    if (strpos($pattern, '${') !== FALSE) {
        $pattern = \testonaut\Utils\Variablestorage::getInstance()->$pattern;
    }
    $matched = $patternobj->match($valueis);
    return new CommandResult(!$matched, !$matched, $matched ? 'Matched and should not have' : 'Correctly did not match ' . $matched);
  }

  protected function verify($valueis, $pattern) {
    
    if (strpos($pattern, '${') !== FALSE) {
        $pattern = \testonaut\Utils\Variablestorage::getInstance()->$pattern;
    }
  
    $patternobj = new Pattern($pattern);
    $matched = $patternobj->match($valueis);   
    return new CommandResult(true, $matched, $matched ? 'Matched' : 'Did not match: ' . $matched);
  }

  protected function verifyNot($valueis, $pattern) {
    $patternobj = new Pattern($pattern);
    
    if (strpos($pattern, '${') !== FALSE) {
        $pattern = \testonaut\Utils\Variablestorage::getInstance()->$pattern;
    }
    $matched = $patternobj->match($valueis);
    return new CommandResult(true, !$matched, $matched ? 'Matched and should not have' : 'Correctly did not match ' . $matched);
  }

  /**
   * @see Selenese\CommandResult::__construct()
   */
  protected function commandResult($continue, $success, $message) {
    return new CommandResult($continue, $success, $message);
  }

  public function arraySearchKey($needle, array $array) {
    for ($i = 0; $i < count($array); $i++) {
      foreach ($array[$i] as $key => $val) {
        if ($val == $needle) {
          return $array[$i]['value'];
        }
      }
    }
    return false;
  }

  public function arrayKeyExist($needle, array $array) {
    for ($i = 0; $i < count($array); $i++) {
      foreach ($array[$i] as $key => $val) {
        if ($array[$i]['name'] == $needle) {
          return true;
        }
      }
    }
    return false;
  }

}
