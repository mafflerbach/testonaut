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


namespace testonaut\Search;

use testonaut\Config;

class File {

  /**
   * @var \SQLite3 $dbInstance
   */
  private $dbInstance;
  private $path;
  private $tableName;

  public function __construct($dbfile, $tableName, $path = '') {

    $this->path = $path;
    $this->tableName = $tableName;

    if (file_exists($dbfile)) {
      $this->dbInstance = new \SQLite3($dbfile);
    } else {
      touch($dbfile);
      $this->dbInstance = new \SQLite3($dbfile);
      $schema = 'CREATE TABLE "' . $tableName . '" (
        "filename" TEXT,
        "type" TEXT,
        "path" TEXT);';
      $this->dbInstance->query($schema);
      $this->initializeIndex();
    }
  }

  public function doIndexing($path = '') {

    if ($path == '') {
      $path = $this->path;
    }
    //$this->clearIndex();
    $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
    /**
     * @var \SplFileInfo $object
     */
    foreach ($objects as $name => $object) {
      if ($object->isDir()) {
        continue;
      }

      $filename = $object->getFilename();
      $filepath = str_replace(Config::getInstance()->Path, '', $object->getPath());

      $file_info = new \finfo(FILEINFO_MIME_TYPE);
      $mime_type = $file_info->buffer(file_get_contents($object->getRealPath()));

      $sql = 'insert into ' . $this->tableName . ' (filename, path, type) VALUES (:filename, :path, :type)';
      $stm = $this->dbInstance->prepare($sql);
      $stm->bindParam(':filename', $filename);
      $stm->bindParam(':path', $filepath);
      $stm->bindParam(':type', $mime_type);
      $stm->execute();
    }
  }

  public function createVirtualTable() {

    if (!$this->exitsTable($this->tableName . 'Search')) {
      $sql = "CREATE VIRTUAL TABLE " . $this->tableName . "Search USING fts4(filename, path, type)";
      $stm = $this->dbInstance->prepare($sql);
      $stm->execute();

      $sql = "INSERT INTO " . $this->tableName . "Search SELECT filename, path, type FROM files";
      $stm = $this->dbInstance->prepare($sql);
      $stm->execute();
    }

  }

  private function clearIndex() {

    if ($this->exitsTable($this->tableName)) {
      $sql = 'delete from ' . $this->tableName;
      $stm = $this->dbInstance->prepare($sql);
      $stm->execute();
    }
  }

  private function clearVirtualTable() {

    if ($this->exitsTable(".$this->tableName." . 'Search')) {
      $sql = 'DROP TABLE ' . ".$this->tableName." . 'Search';
      $stm = $this->dbInstance->prepare($sql);
      $stm->execute();
    }
  }

  public function updateIndex() {

    $this->clearIndex();
    $this->clearVirtualTable();

    $this->doIndexing();
    $this->createVirtualTable();
  }

  public function initializeIndex() {

    $this->clearIndex();
    $this->clearVirtualTable();

    $this->doIndexing();
    $this->createVirtualTable();
  }

  private function exitsTable($name) {

    $sql = "SELECT count(name) as count FROM sqlite_master WHERE type='table' AND name='" . $name . "'";
    $stm = $this->dbInstance->prepare($sql);
    $result = $stm->execute()
      ->fetchArray()
    ;

    if ($result['count'] == 0) {
      return FALSE;
    }

    return TRUE;
  }

  public function search($term) {

    $sql = "SELECT * FROM " . $this->tableName . "Search WHERE " . $this->tableName . "Search.filename MATCH :term";
    $stm = $this->dbInstance->prepare($sql);
    $stm->bindParam(':term', $term);
    $result = $stm->execute();

    $return = array();

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
      $return [] = $row;
    }

    return $return;
  }

}