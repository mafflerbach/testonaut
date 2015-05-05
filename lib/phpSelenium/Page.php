<?php

namespace phpSelenium;

use phpSelenium\Selenium\Api;

class Page {
  public $path;
  protected $root;

  public function __construct($path) {
    $this->path = $path;
    $this->root = Config::getInstance()->wikiPath;
  }

  public function getCompiledPage() {
    return $this->_content(NULL, NULL, TRUE);
  }

  private function _content($content = NULL, $save = NULL, $compiled = FALSE) {

    if ($this->path == '') {
      $file = $this->root . '/content';
      $path = $this->root;
    } else {
      $file = $this->transCodePath() . '/content';
      $path = $this->transCodePath();
      if ($compiled && file_exists($file . '_compiled')) {
        $file = $this->transCodePath() . '/content_compiled';
      }
    }

    if (!file_exists($file) && $save === NULL) {
      return '';
    }
    if ($content == NULL && $save === NULL) {
      $pageContent = file_get_contents($file);
      return $pageContent;
    } else {
      $filename = $path . '/content';
      if (!is_dir($path)) {
        if (!mkdir($path, 0775, TRUE)) {
          throw new \Exception();
        }
      }
      file_put_contents($filename, $content);
    }

  }

  public function content($content = NULL, $save = NULL) {
    return $this->_content($content, $save);
  }

  public function getImagePath() {
    return \phpSelenium\Config::getInstance()->imageRoot . "/" . $this->relativePath();
  }

  public function getFilePath() {
    return \phpSelenium\Config::getInstance()->fileRoot . "/" . $this->relativePath();
  }

  public function getResultPath() {
    return \phpSelenium\Config::getInstance()->result . "/" . $this->relativePath();
  }

  public function getLinkedFiles() {
    $files = array(
      'images'    => array(),
      'documents' => array()
    );
    $linkDir = $this->getFilePath();
    /**
     * @var $fileInfo
     */

    if (file_exists($linkDir)) {

      foreach (new \DirectoryIterator($linkDir) as $fileInfo) {
        if ($fileInfo->isDot()) {
          continue;
        }
        if ($fileInfo->isDir()) {
          continue;
        }

        $filenameLink = $this->relativePath() . '/' . $fileInfo->getFilename();
        $filename = $linkDir . '/' . $fileInfo->getFilename();

        $file_info = new \finfo(FILEINFO_MIME);
        $mime_type = $file_info->buffer(file_get_contents($filename));
        if (strpos($mime_type, 'image') !== FALSE) {
          $files['images'][] = $filenameLink;
        } else {
          $files['documents'][] = $fileInfo->getFilename();
        }
      }
    }
    return $files;
  }

  public function getImages() {
    $imageDir = $this->getImagePath();
    $return = array();

    $api = new Api();
    $browser = $api->getBrowserList();
    for ($i = 0; $i < count($browser); $i++) {
      $name = str_replace(' ', '_', $browser[$i]['browserName']);
      if (file_exists($imageDir . "/" . $name . "/src/")) {
        $src = array_diff(scandir($imageDir . "/" . $name . "/src/"), array(
          '.',
          '..'
        ));
        $return[$name]['src'] = $src;
      }
      if (file_exists($imageDir . "/" . $name . "/comp/")) {
        $src = array_diff(scandir($imageDir . "/" . $name . "/comp/"), array(
          '.',
          '..'
        ));
        $return[$name]['comp'] = $src;
      }
      if (file_exists($imageDir . "/" . $name . "/ref/")) {
        $src = array_diff(scandir($imageDir . "/" . $name . "/ref/"), array(
          '.',
          '..'
        ));
        $return[$name]['ref'] = $src;
      }
    }

    return $return;
  }

  public function transCodePath() {
    return str_replace('.', '/', $this->root . '/' . $this->path);
  }

  public function relativePath() {
    return str_replace('.', '/', $this->path);
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

  public function rename($path, $newPath) {
    $path = str_replace('.', '/', $path);
    $newPath = str_replace('.', '/', $newPath);

    $path = Config::getInstance()->wikiPath . '/' . $path;
    $newPath = Config::getInstance()->wikiPath . '/' . $newPath;

    if (!file_exists($newPath)) {
      return @rename($path, $newPath);
    }
    return FALSE;
  }

  public function getEditPath() {
    return '/edit/' . $this->path;
  }
} 