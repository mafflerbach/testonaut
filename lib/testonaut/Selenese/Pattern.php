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

class Pattern {

  public $type;
  public $value;

  // todo: support comma separated lists of values
  public function __construct($pattern) {
    $explode = explode(':', $pattern, 2);
    if (count($explode) == 2 && in_array($explode[0], array(
          'glob',
          'regex',
          'regexp',
          'regexpi',
          'exact'
        ))
    ) {
      $this->type = $explode[0];
      $this->value = $explode[1];
    } else {
      $this->type = 'glob';
      $this->value = $pattern;
    }
  }

  /**
   * @param string $content
   * @throws \Exception
   * @return bool
   */
  public function match($content) {
    switch ($this->type) {
      case 'glob':
        // convert the glob to a regex, cause glob will choke on long strings, like the entire source of a page
        $regex = str_replace(array(
            "\*",
            "\?"
          ), // wildcard chars
          array(
            '.*',
            '.'
          ),   // regexp chars
          preg_quote($this->value, '/'));
          
        return (bool)preg_match('/' . $regex . '/U', $content);
        break;
      case 'regexpi':
        $flags = 'i';
      case 'regexp':
      case 'regex':
        if (!isset($flags)) {
          $flags = '';
        }
        return (bool)preg_match('/' . $this->value . '/' . $flags, $content);
        break;
      case 'exact':
        return $content == $this->value;
        break;
      default:
        throw new \Exception("Unsupported pattern matching type: " . $this->type);
        break;
    }
  }

}
