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



namespace testonaut;

use testonaut\Selenium\Api;
use testonaut\Settings\Profile;

/**
 * Class Page
 *
 * @package testonaut
 */
class Page {

  /**
   * @public
   */
  public $path;
  /**
   * @public void
   */
  protected $root;

  /**
   * @param $path
   */
  public function __construct($path) {

    $this->path = $path;
    $this->root = Config::getInstance()->wikiPath;
  }

  /**
   * @throws \Exception
   */
  public function getCompiledPage(){
      $compiler = new Page\Compiler($this);
      return $compiler->getContent();
  }

  /**
   * @param null $content
   * @param null $save
   * @param bool $compiled
   * @throws \Exception
   *
   * @return String
   */
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

  /**
   * @param null $content
   * @param null $save
   * @throws \Exception
   *
   * @return String
   */
  public function content($content = NULL, $save = NULL) {

    return $this->_content($content, $save);
  }

  /**
   * @return string
   */
  public function getImagePath() {

    return \testonaut\Config::getInstance()->imageRoot . "/" . $this->relativePath();
  }

  /**
   * @return string
   */
  public function getFilePath() {

    return \testonaut\Config::getInstance()->fileRoot . "/" . $this->relativePath();
  }

  /**
   * @return string
   */
  public function getResultPath() {

    return \testonaut\Config::getInstance()->result . "/" . $this->relativePath();
  }

  /**
   * @return array
   */
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

  /**
   * @return array
   */
  public function getImages() {

    $imageDir = $this->getImagePath();
    $return = array();
    $profile = new Profile();
    $profile = $profile->get();
    $browser = $profile['all'];

    for ($i = 0; $i < count($browser); $i++) {
      if (isset($browser[$i]['browserName'])) {
        $name = str_replace(' ', '_', $browser[$i]['browserName']).'_default';
      } else {
        $name = str_replace(' ', '_', $browser[$i]['name']).'_'.$browser[$i]['browser'];
      }

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

  /**
   * @return mixed
   */
  public function transCodePath() {

    return str_replace('.', '/', $this->root . '/' . $this->path);
  }

  /**
   * @return mixed
   */
  public function relativePath() {

    return str_replace('.', '/', $this->path);
  }

  /**
   * @param array $config
   * @return bool
   */
  public function config($config = array()) {

    $file = $this->transCodePath() . '/config';
    if (empty($config)) {
      if (!file_exists($file) && is_dir($this->transCodePath())) {
        file_put_contents($file, '{"type":"static","browser":[]}');
      }
      if (file_exists($file)) {
        return json_decode(file_get_contents($file), TRUE);
      }
    } else {
      if (file_exists($file)) {
        $conf = json_decode(file_get_contents($this->transCodePath() . '/config'), TRUE);
        foreach ($config as $key => $val) {
          $conf[$key] = $val;
        }
        if (!file_put_contents($file, json_encode($conf))) {
          return FALSE;
        };
      }

      return TRUE;
    }
  }

  /**
   *
   */
  public function delete() {
    return $this->_delete($this->transCodePath());
  }

  /**
   * @param $dir
   * @return bool
   */
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

  /**
   * @return mixed
   */
  public function getPath() {

    return $this->path;
  }

  /**
   * @param $path
   * @param $newPath
   * @return bool
   */
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

  /**
   * @return string
   */
  public function getEditPath() {

    return '/edit/' . $this->path;
  }
} 