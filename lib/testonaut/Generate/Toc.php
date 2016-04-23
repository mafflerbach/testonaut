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
            $ritit->getSubIterator($depth)
              ->current()
              ->getFilename() => $path
          );
        }
        $r = array_merge_recursive($r, $path);
      }
    }
    ksort($r);
    $this->dirArray = $r;
  }

  public function run2() {

    if (!file_exists($this->basePath)) {
      return '';
    }
    $ritit = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->basePath), \RecursiveIteratorIterator::CHILD_FIRST);
    $dirs = array();
    $r = array();
    /**
     * @var  \SplFileInfo $splFileInfo
     */
    foreach ($ritit as $splFileInfo) {
      if ($splFileInfo->getFilename() == '.' || $splFileInfo->getFilename() == '..' || strpos($splFileInfo->getPath(), '.git') !== FALSE) {
        continue;
      }

      if ($splFileInfo->isDir() && !in_array($splFileInfo->getFilename(), $dirs) && $splFileInfo->getFilename() !== '.git') {
        $path = array($splFileInfo->getFilename() => array());
        for ($depth = $ritit->getDepth() - 1; $depth >= 0; $depth--) {
          $path = array(
            $ritit->getSubIterator($depth)
              ->current()
              ->getFilename() => $path
          );
        }
        $r = array_merge_recursive($r, $path);
      }
    }
    ksort($r);

    $this->dirArray = $r;
  }

  /**retuns a ul with all pages
   *
   * @return string
   */
  public function generateMenu() {

    return '<div class="contentList"><h5>Contentlist</h5>' . $this->makeList($this->dirArray) . '</div>';
  }

  /**
   * return an html ul with the wiki pages
   *
   * @param        $array
   * @param string $path
   *
   * @return string
   */
  protected function makeList($array, $path = '', $tree = '', $level = 0) {
    ksort($array);
    $level++;
    foreach ($array as $key => $value) {
      if (is_array($value)) {
        if ($path != '') {
          $_path = $path . '.' . $key;
        } else {
          $_path = $key;
        }
        $prefix = '';
        if ($this->page != '') {
          $prefix = $this->page . '.';
        }
        $link = '<a href="' . Config::getInstance()->appPath . '/web/' . $prefix . $_path . '">' . $key . '</a>';
        $tree .= '<li> <span class="level' . $level . '"></span>' . $link;
        $tree .= '' . $this->makeList($value, $_path, '', $level);
        $tree .= '</li>';
      } else {
        if ($value == 'content' || $value == 'config') {
          continue;
        }
      }
    }
    if ($tree != '') {
      $tree = '<ul class="ascii">' . $tree . '</ul>';
    }

    return $tree;
  }
}