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

namespace testonaut\Generate;

use testonaut\Config;
use testonaut\Parser\Config\Browser;
use testonaut\Selenium\Api;

/**
 * Class Toc
 *
 * @package testonaut\Generate
 */
class Toc {

  /**
   * @public string
   */
  private $page = '';
  /**
   * @public string
   */
  private $basePath = 'root';
  /**
   * @public array
   */
  private $dirArray = array();

  /**
   * @param string $basePath
   */
  public function __construct($basePath = 'root') {

    if ($basePath != 'root') {
      $this->basePath = $basePath;
    }
  }

  /**
   * @param $page
   */
  public function page($page) {

    $this->page = $page;
  }

  /**
   *
   */
  public function runDir() {

    if (!file_exists($this->basePath)) {
      return '';
    }
    $ritit = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->basePath), \RecursiveIteratorIterator::CHILD_FIRST);
    $dirs = array();
    $r = array();
    foreach ($ritit as $splFileInfo) {
      if ($splFileInfo->getFilename() == '.' || $splFileInfo->getFilename() == '..' || strpos($splFileInfo->getPath(), '.git') !== FALSE) {
        continue;
      }

      if ($splFileInfo->isDir() && !in_array($splFileInfo->getFilename(), $dirs) && $splFileInfo->getFilename() !== '.git') {
        $path = array($splFileInfo->getFilename() => array());
        for ($depth = $ritit->getDepth() - 1; $depth >= 0; $depth--) {
          $path = array(
            $ritit->getSubIterator($depth)->current()->getFilename() => $path
          );
        }
        $r = array_merge_recursive($r, $path);
      }
    }
    ksort($r);

    $xml_data = new \SimpleXMLElement('<?xml version="1.0"?><toc></toc>');
    $this->array_to_xml($r, $xml_data, true);

    return $xml_data;
  }

  private function array_to_xml($data, &$xml_data, $ignoreAssoc = false) {
    /**
     * @var \SimpleXMLElement $xml_data
     */
    foreach ($data as $key => $value) {
      if (is_array($value)) {
        if (is_numeric($key)) {
          $key = 'item' . $key;
        }

        if (strpos($key, ' ') !== FALSE || $ignoreAssoc) {
          $c = $key;
          $key = 'item';
          $subnode = $xml_data->addChild($key);
          $subnode->addAttribute('name', $c);
        } else {
          $subnode = $xml_data->addChild($key);
        }

        $this->array_to_xml($value, $subnode, $ignoreAssoc);

      } else {
        $xml_data->addChild("$key", htmlspecialchars("$value"));
      }
    }
  }

}