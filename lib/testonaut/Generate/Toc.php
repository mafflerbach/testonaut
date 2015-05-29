<?php

namespace testonaut\Generate;

use testonaut\Config;
use testonaut\Parser\Config\Browser;
use testonaut\Selenium\Api;

class Toc {

  private $page = '';
  private $basePath = 'root';
  private $dirArray = array();

  public function __construct($basePath = 'root') {
    if ($basePath != 'root') {
      $this->basePath = $basePath;
    }
  }

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
    return '<div class="contentList"><h5>Contentlist</h5>'.$this->makeList($this->dirArray).'</div>';
  }

  /**
   * return an html ul with the wiki pages
   *
   * @param        $array
   * @param string $path
   *
   * @return string
   */
  protected function makeList($array, $path = '', $tree = '',  $level=0) {
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
          $prefix = $this->page.'.';
        }
        $link = '<a href="'.Config::getInstance()->appPath.'/web/'.$prefix . $_path . '">' . $key . '</a>';
        $tree .= '<li> <span class="level'.$level.'"></span>' . $link;
        $tree .= ''.$this->makeList($value, $_path, '', $level);
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

}