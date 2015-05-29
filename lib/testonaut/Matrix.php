<?php
/**
 * Created by PhpStorm.
 * User: maren
 * Date: 19.04.2015
 * Time: 21:25
 */

namespace testonaut;

class Matrix {
  private $page;
  private $browsers;

  public function __construct(Page $page, $browsers) {
    $this->page = $page;
    $this->browsers = $browsers;
  }

  public function read() {
    $summery = array();
    $dir = $this->page->getResultPath();
    $path = $this->page->getResultPath();

    $fileByBrowser = array();
    if (file_exists($dir)) {

      $dir = new \DirectoryIterator($dir);
      foreach ($dir as $fileinfo) {
        if (!$fileinfo->isDot()) {
          $filename = $fileinfo->getFilename();
          for ($i = 0; $i < count($this->browsers); $i++) {
            $bName = $this->browsers[$i]['browserName'];
            if (strpos($filename, $bName) !== FALSE) {
              $fileByBrowser[$bName][] = $filename;
            }
          }
        }
      }

      foreach ($fileByBrowser as $browser => $file) {
        $foo = $path . '/' . $file[count($file) - 1];
        $result = json_decode(file_get_contents($foo), TRUE);
        for ($i = 0; $i < count($result); $i++) {
          $summery[$browser] = $result[$i]['browserResult'];
        }
      }
      return $summery;
    }
  }

  public function write($result) {

  }
}