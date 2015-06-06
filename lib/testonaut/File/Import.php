<?php
/**
 * Created by PhpStorm.
 * User: maren
 * Date: 06.06.2015
 * Time: 18:24
 */
namespace testonaut\File;

class Import {

  private $locationTo;
  private $filename;
  private $page;

  public function __construct($locationTo, $filename, $page) {

    $this->locationTo = $locationTo;
    $this->filename = $filename;
    $this->page = $page;

  }

  /**
   * @throws Import\Exception
   */
  public function doImport() {
    if (!$this->unpackFile($this->locationTo, $this->filename)) {
      throw new \testonaut\File\Import\Exception('Could not Unzip');
    }
    if (!$this->convertImportToWiki(str_replace('zip', 'unzip', $this->locationTo))) {
      throw new \testonaut\File\Import\Exception('Could not convert Zipfile Structure');
    }

    if (!$this->moveImportToWikiDir($this->locationTo, $this->page->transCodePath())) {
      throw new \testonaut\File\Import\Exception('Could not move import file to wiki');
    }
    
    return TRUE;
  }

  /**
   * @param $path
   * @param $filename
   * @return bool
   */
  protected function unpackFile($path, $filename) {

    $file = $path . '/zip/' . $filename;
    $zip = new \ZipArchive();
    $res = $zip->open($file);
    if ($res === TRUE) {
      $zip->extractTo($path . '/unzip');
      $zip->close();
      return $res;
    } else {
      return $res;
    }
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
   * @param $path
   * @return bool
   */
  protected function convertImportToWiki($path) {
    $flag  = TRUE;
    $unzipPath = $path . DIRECTORY_SEPARATOR . 'unzip';
    $transformPath = $path . DIRECTORY_SEPARATOR . 'transform';

    if (!file_exists($transformPath)) {
      mkdir($transformPath, 0777, TRUE);
    }

    $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($unzipPath), \RecursiveIteratorIterator::SELF_FIRST);
    /**
     * @var \SplFileInfo $object
     */
    foreach ($objects as $name => $object) {
      if ($object->getFilename() == '.' || $object->getFilename() == '..') {
        continue;
      }

      $newPath = str_replace($unzipPath, $transformPath, $object->getPath()) . DIRECTORY_SEPARATOR . $object->getFilename();
      if (is_dir($object->isDir())) {
        mkdir($newPath, 0777, TRUE);
      }

      if ($object->isFile()) {
        if (!$this->renameFileToContentFile($newPath, $object)) {
          $flag = FALSE;
        }
      }
    }

    return $flag;
  }

  /**
   * @param $newPath
   * @param $object
   * @return bool
   */
  protected function renameFileToContentFile($newPath, $object) {

    $dirname = str_replace('.html', '', $newPath);
    $newPagePath = str_replace('.html', '', $newPath);
    if (!file_exists($newPagePath)) {
      mkdir($newPagePath, 0777, TRUE);
    }
    $unzipFile = $object->getPath() . DIRECTORY_SEPARATOR . $object->getFilename();
    file_put_contents($dirname . '/config', '{"type":"test","browser":{"urls":null,"active":null},"screenshots":"none"}');
    return copy($unzipFile, $dirname . '/content');
  }

  protected function moveImportToWikiDir($path, $toDir) {
    if ($this->rcopy($path . '/transform', $toDir)) {
      return $this->_delete($path);
    }
    return FALSE;
  }

  /**
   * Recursively copy files from one directory to another
   *
   * @param String $src  - Source of files being moved
   * @param String $dest - Destination of files being moved
   * @return bool
   */
  protected function rcopy($src, $dest) {

    if (!is_dir($src)) {
      return FALSE;
    }

    if (!is_dir($dest)) {
      if (!mkdir($dest)) {
        return FALSE;
      }
    }

    $i = new \DirectoryIterator($src);
    foreach ($i as $f) {
      if ($f->isFile()) {
        copy($f->getRealPath(), "$dest/" . $f->getFilename());
      } else if (!$f->isDot() && $f->isDir()) {
        $this->rcopy($f->getRealPath(), "$dest/$f");
      }
    }
    return TRUE;
  }

}