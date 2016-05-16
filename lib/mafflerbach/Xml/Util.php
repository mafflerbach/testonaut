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


namespace mafflerbach\Xml;


class Util extends \DOMDocument {


  public function __construct() {
    parent::__construct();
  }

  public function node_create($arr, $items = null, $useAttributes = false, $rootName = 'items') {
    $name = '';
    if (is_null($items)) {
      $items = $this->appendChild($this->createElement($rootName));
    }

    foreach ($arr as $element => $value) {
      if (is_numeric($element)) {
        $element = 'item';
      }
      if (strpos($element, ' ') !== FALSE || $useAttributes) {
        $name = $element;
        $element = 'item';

      }

      if ($value instanceof Util) {
        $me = $this->createDocumentFragment();

        $tmp = $value->saveXML();
        $tmp = str_replace('<?xml version="1.0"?>', '', $tmp);

        $me->appendXML($tmp);
        $fragment = $me->cloneNode(true);
      } else {
        $fragment = $this->createElement($element, (is_array($value) ? null : $value));
      }

      if ($useAttributes) {
        $fragment->setAttribute('name', $name);
        $name = '';
      }

      if ($name != '' ) {
        $fragment->setAttribute('name', $name);
        $name = '';
      }

      $items->appendChild($fragment);

      if (is_array($value)) {
        self::node_create($value, $fragment, $useAttributes);
      }
    }
  }

}