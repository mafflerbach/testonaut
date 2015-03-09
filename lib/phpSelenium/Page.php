<?php

namespace phpSelenium;

class Page {
  protected $path;
  protected $root;

  public function __construct($path) {
    $this->path = $path;
    $this->root = Config::getInstance()->wikiPath;
  }

  public function content($content = NULL, $save = NULL) {
    $file = $this->transCodePath() . '/content';
    if (!file_exists($file) && $save === NULL) {
      return '';
    }
    if ($content == null && $save === NULL) {
      $pageContent = file_get_contents($file);
      return $pageContent;
    } else {
      $filename = $this->transCodePath() . '/content';
      if (!is_dir($this->transCodePath())) {
        print($this->transCodePath());
        if (!mkdir($this->transCodePath(), 0755, true)) {
          throw new \Exception();
        }
      }
      file_put_contents($filename, $content);
    }
  }

  public function getImages() {

  }

  public function transCodePath() {
    return str_replace('.', '/', $this->root . '/' . $this->path);
  }

  public function config($config = array()) {
    if (empty($config)) {
      $file = $this->transCodePath() . '/config';
      if (!file_exists($file) && is_dir($this->transCodePath())) {
        file_put_contents($this->transCodePath() . '/config', '{"type":"static","browser":[]}');
      }
      if (file_exists($file)) {
        return json_decode(file_get_contents($this->transCodePath() . '/config'), true);
      }
    } else {
      $conf = json_decode(file_get_contents($this->transCodePath() . '/config'), true);
      foreach($config as $key => $val) {
        $conf[$key] = $val;
      }
      if (!file_put_contents($this->transCodePath() . '/config', json_encode($conf))) {
          return FALSE;
      };
      return TRUE;
    }
  }

  public function delete() {
    $this->_delete($this->transCodePath());
  }

  protected function _delete($dir) {
    $files = array_diff(scandir($dir), array(
        '.',
        '..'
      ));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? $this->_delete("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
  }

  public function getPath() {
    return $this->path;
  }

} 