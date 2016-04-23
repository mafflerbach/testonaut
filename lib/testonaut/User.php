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


/**
 * Class User
 * @package testonaut
 */
class User {
  /**
   * @var \SQLite3 $db
   */
  protected $db;
  public function __construct() {
    $db = new \testonaut\Utils\Db(Config::getInstance()->Path . '/index.db');
    $this->db = $db->getInstance();
  }

  public function validate($name, $password) {
    $sql = 'select * from "user" WHERE email=:email';
    $stm = $this->db->prepare($sql);
    $stm->bindParam(':email', $name);
    $result = $stm->execute();
    $res = $result->fetchArray(SQLITE3_ASSOC);
    if (password_verify($password, $res['password'])) {
      $_SESSION['testonaut']['userId'] = $res['id'];
      return true;
    }
    return false;
  }


  public function add($name, $password) {
    $sql = 'insert into user (email, password) VALUES (:email, :password)';
    $stm = $this->db->prepare($sql);
    $stm->bindParam(':email', $name);
    $password = password_hash($password, PASSWORD_DEFAULT);
    $stm->bindParam(':password', $password);

    $stm->execute();

  }

  public function remove($name) {

  }

  public function exist($email) {
    $sql = 'select count(*) as count from user WHERE email=:email';
    $stm = $this->db->prepare($sql);
    $stm->bindParam(':email', $email);
    $result = $stm->execute();
    $res = $result->fetchArray(SQLITE3_ASSOC);

    if ($res['count'] > 0) {
      return TRUE;
    }
    return FALSE;
  }

}