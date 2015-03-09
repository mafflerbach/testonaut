<?php

namespace phpSelenium;

use phpSelenium\Parser\Config\Browser;

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
    if ($content == NULL && $save === NULL) {
      $pageContent = file_get_contents($file);
      return $pageContent;
    } else {
      $filename = $this->transCodePath() . '/content';
      if (!is_dir($this->transCodePath())) {
        print($this->transCodePath());
        if (!mkdir($this->transCodePath(), 0755, TRUE)) {
          throw new \Exception();
        }
      }
      file_put_contents($filename, $content);
    }
  }

  public function getImagePath() {
    return \phpSelenium\Config::getInstance()->appPath . "/".$this->relativePath() . '/__IMAGES';
  }

  public function getImages() {
    $imageDir = $this->transCodePath() . '/__IMAGES';
    $return = array();

    $browser = new \phpSelenium\Parser\Config\Browser();
    $browser->config(\phpSelenium\Config::getInstance()->seleniumConsole);

    for ($i = 0; $i < count($browser->browser); $i++) {
      $name = $browser->browser[$i]['browserName'];
      if (file_exists($imageDir . "/src/" . $name)) {
        $src = array_diff(scandir($imageDir . "/src/" . $name), array(
          '.',
          '..'
        ));
        $return['src'][$name] = $src;
      }
      if (file_exists($imageDir . "/comp/" . $name)) {
        $src = array_diff(scandir($imageDir . "/comp/" . $name), array(
          '.',
          '..'
        ));
        $return['comp'][$name] = $src;
      }
      if (file_exists($imageDir . "/ref/" . $name)) {
        $src = array_diff(scandir($imageDir . "/ref/" . $name), array(
          '.',
          '..'
        ));
        $return['ref'][$name] = $src;
      }
    }

    return $return;
  }

  public function transCodePath() {
    return str_replace('.', '/', $this->root . '/' . $this->path);
  }

  public function relativePath() {
    return str_replace('.', '/', 'root/' . $this->path);
  }

  public function config($config = array()) {
    if (empty($config)) {
      $file = $this->transCodePath() . '/config';
      if (!file_exists($file) && is_dir($this->transCodePath())) {
        file_put_contents($this->transCodePath() . '/config', '{"type":"static","browser":[]}');
      }
      if (file_exists($file)) {
        return json_decode(file_get_contents($this->transCodePath() . '/config'), TRUE);
      }
    } else {
      $conf = json_decode(file_get_contents($this->transCodePath() . '/config'), TRUE);
      foreach ($config as $key => $val) {
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