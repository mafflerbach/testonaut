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

    $profile = 'CREATE TABLE "profile" (
        "browser" TEXT,
        "name" TEXT PRIMARY KEY NOT NULL,
        "driverOptions" TEXT,
        "arguments" TEXT,
        "capabilities" TEXT,
        "os" TEXT,
        "version" TEXT );';

    $imageCompare = 'CREATE TABLE "imageCompare" (
        "date" TEXT,
        "path" TEXT,
        "result" TEXT,
        "webpath" TEXT,
        "profile" TEXT,
        "imageName" TEXT,
        "images" TEXT);';

    $user ='CREATE TABLE "User" (
            "id" INTEGER PRIMARY KEY AUTOINCREMENT,
            "email" TEXT NOT NULL,
            "password" TEXT NOT NULL,
            "displayName" TEXT NOT NULL,
            "active" INT NOT NULL,
            "group" INTEGER
        );';

    $addRoot = 'insert into "User" 
          (email, password, displayName, active , "group") values 
          (\'root\',\'$2y$10$m/DbEBFPqPMYRLNVWpCTPuoQvCaYbBXs4heL7GzCSBvKHtsKp.uaS\', \'root\', 1, 1)';

    $this->dbInstance->query($files);
    $this->dbInstance->query($profile);
    $this->dbInstance->query($history);
    $this->dbInstance->query($imageCompare);
    $this->dbInstance->query($user);
    $this->dbInstance->query($addRoot);
  }
  
  public function getInstance() {
    return $this->dbInstance;
  }
  
}
