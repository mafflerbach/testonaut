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


class Util {


  public function array_to_xml($data, &$xml_data, $ignoreAssoc = false) {
    /**
     * @var \SimpleXMLElement $xml_data
     */
    foreach ($data as $key => $value) {
      if (is_array($value)) {
        if (is_numeric($key)) {
          $key = 'item';
        }

        if (strpos($key, ' ') !== FALSE) {
          $c = $key;
          $key = 'item';
          $subnode = $xml_data->addChild($key);
          $subnode->addAttribute('name', $c);
        } else {
          $subnode = $xml_data->addChild($key);
        }

        $this->array_to_xml($value, $subnode, $ignoreAssoc);

      } else {

        if (gettype($value) == 'object' && get_class($value) == 'SimpleXMLElement') {
          /**
           * @var \SimpleXMLElement $value
           */
          $this->simplexml_import_xml($xml_data, str_replace('<?xml version="1.0"?>', '', $value->saveXML()));
        } else {
          $xml_data->addChild("$key", htmlspecialchars("$value"));
        }
      }
    }
  }

  private function simplexml_import_xml(\SimpleXMLElement $parent, $xml, $before = false)
  {
    $xml = (string)$xml;

    // check if there is something to add
    if ($nodata = !strlen($xml) or $parent[0] == NULL) {
      return $nodata;
    }

    // add the XML
    $node     = dom_import_simplexml($parent);
    $fragment = $node->ownerDocument->createDocumentFragment();
    $fragment->appendXML($xml);

    if ($before) {
      return (bool)$node->parentNode->insertBefore($fragment, $node);
    }

    return (bool)$node->appendChild($fragment);
  }

}