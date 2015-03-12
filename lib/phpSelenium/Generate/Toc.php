<?php

namespace phpSelenium\Generate;

use phpSelenium\Parser\Config\Browser;
use phpSelenium\Selenium\Api;

class Toc {

  private $basePath = 'root';
  private $dirArray = array();

  public function __construct($basePath = 'root') {
    if ($basePath != 'root') {
      $this->basePath = $basePath;
    }
  }

  /**
   *
   */
  public function runDir() {
    $ritit = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->basePath), \RecursiveIteratorIterator::CHILD_FIRST);
    $dirs = array(
      '__IMAGES',
      '.png',
      'src',
      'ref',
      'comp',
    );

    $dirs = $this->appendBrowserList($dirs);

    $r = array();
    foreach ($ritit as $splFileInfo) {

      if ($splFileInfo->getFilename() == '.' || $splFileInfo->getFilename() == '..') {
        continue;
      }

      if ($splFileInfo->isDir() && !in_array($splFileInfo->getFilename(), $dirs)) {
        $path = array($splFileInfo->getFilename() => array());

        for ($depth = $ritit->getDepth() - 1; $depth >= 0; $depth--) {
          $path = array($ritit->getSubIterator($depth)->current()->getFilename() => $path);
        }
        $r = array_merge_recursive($r, $path);
      }
    }
    $this->dirArray = $r;
  }

  /**retuns a ul with all pages
   * @return string
   */
  public function generateMenu() {
    return $this->makeList($this->dirArray);
  }

  /**
   * return an html ul with the wiki pages
   *
   * @param        $array
   * @param string $path
   *
   * @return string
   */
  protected function makeList($array, $path = '', $tree = '') {

    foreach ($array as $key => $value) {
      if (is_array($value)) {
        if ($path != '') {
          $_path = $path . '.' . $key;
        } else {
          $_path = $key;
        }

        $link = '<a href="' . $_path . '">' . $key . '</a>';
        $tree .= '<li>' . $link;
        $tree .= $this->makeList($value, $_path);
        $tree .= '</li>';

      } else {
        if ($value == 'content' || $value == 'config') {
          continue;
        }
      }
    }

    if ($tree != '') {
      $tree = '<ul>' . $tree . '</ul>';
    }

    return $tree;
  }

  private function appendBrowserList($dirs) {
    $api = new Api();
    $list = $api->getBrowserList();

    for ($i = 0; $i < count($list); $i++) {
      $dirs[] = $list[$i]['browserName'];
    }

    return $dirs;
  }

}