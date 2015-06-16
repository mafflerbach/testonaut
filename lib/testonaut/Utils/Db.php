<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Db
 *
 * @author maren
 */

namespace testonaut\Utils;

class Db {
  private $dbInstance;
  public function __construct($dbfile) {
    
    if (file_exists($dbfile)) {
      $this->dbInstance = new \SQLite3($dbfile);
    } else {
      touch($dbfile);
      $this->dbInstance = new \SQLite3($dbfile);
      $this->init();
    }
  }

  public function init() {

    $files = 'CREATE TABLE "files" (
        "filename" TEXT,
        "type" TEXT,
        "path" TEXT);';

    $history = 'CREATE TABLE "history" (
        "browser" TEXT,
        "date" TEXT,
        "run" BLOB,
        "path" TEXT,
        "filename" TEXT,
        "result" TEXT);';

    $this->dbInstance->query($files);
    $this->dbInstance->query($history);
  }
  
  public function getInstance() {
    return $this->dbInstance;
  }
  
}
