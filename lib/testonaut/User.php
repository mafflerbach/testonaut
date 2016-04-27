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

use testonaut\Page\Provider\Globalconfig;
use Toyota\Component\Ldap\Core\Manager;
use Toyota\Component\Ldap\Platform\Native\Driver;
use Toyota\Component\Ldap\Platform\Native\Search;


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
    $globalConf = new Globalconfig();
    $configuration = $globalConf->getConfig();

    if ($configuration['useLdap']) {
      return $this->ldapValidate($name, $password);
    } else {
      return $this->internValidate($name, $password);
    }
  }

  protected function internValidate($name, $password) {
    $sql = 'select * from "user" WHERE email=:email and active <= 1';
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

  protected function ldapValidate($name, $password) {
    $globalConf = new Globalconfig();
    $configuration = $globalConf->getConfig();

    $params = array(
      'hostname' => $configuration['ldapHostname'],
      'base_dn' => $configuration['ldapBaseDn'],
    );

    $ldap = new Manager($params, new Driver());
    $ldap->connect();
    $ldap->bind($configuration['ldapCn'], $configuration['ldapPassword']);

    $node = $ldap->getNode('uid=' . $name . ',ou=People,dc=example,dc=com');
    $userPassword = $node->get('userPassword')->getValues();

    if ($this->check_password($password, $userPassword[0])) {
      $_SESSION['testonaut']['userId'] = $node->get('uidNumber')->getValues();
      return TRUE;
    } else {
      return FALSE;
    }

  }

  public function save($name, $password, $displayName, $id) {
    $sql = 'update user set email= :email, password= :password, displayName= :displayName where id=:id';
    $stm = $this->db->prepare($sql);
    $stm->bindParam(':email', $name);
    $stm->bindParam(':displayName', $displayName);
    $password = password_hash($password, PASSWORD_DEFAULT);
    $stm->bindParam(':password', $password);
    $stm->bindValue(':id', $id);

    $result = $stm->execute();

    if ($result === FALSE) {
      return FALSE;
    } else {
      return TRUE;
    }

  }

  public function add($name, $password, $displayName) {
    $sql = 'insert into user (email, password, displayName, active) VALUES (:email, :password, :displayName, :active)';
    $stm = $this->db->prepare($sql);
    $stm->bindParam(':email', $name);
    $stm->bindParam(':displayName', $displayName);
    $password = password_hash($password, PASSWORD_DEFAULT);
    $stm->bindParam(':password', $password);
    $stm->bindValue(':active', 1);

    $stm->execute();
  }

  public function delete($id) {
    $sql = 'delete from user where id=:id';
    $stm = $this->db->prepare($sql);
    $stm->bindValue(':id', $id);

    $result = $stm->execute();

    if ($result === FALSE) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  public function changeStatus($id, $bool) {
    if ($bool) {
      $sql = 'update user set active=1 where id=:id';
    } else {
      $sql = 'update user set active=0 where id=:id';
    }

    $stm = $this->db->prepare($sql);
    $stm->bindValue(':id', $id);

    $result = $stm->execute();

    if ($result === FALSE) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  public function reset($name) {
    $sql = 'update user set password=:password where email=:email';

    $stm = $this->db->prepare($sql);
    $stm->bindValue(':email', $name);

    $passwordGen = $this->generateRandomString();
    $password = password_hash($passwordGen, PASSWORD_DEFAULT);
    $stm->bindValue(':password', $password);
    $result = $stm->execute();

    if ($result === FALSE) {
      return FALSE;
    } else {
      return array('result' => TRUE, 'password' => $passwordGen);
    }
  }

  private function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }


  public function get($id) {
    $globalConf = new Globalconfig();
    $configuration = $globalConf->getConfig();

    if ($configuration['useLdap']) {
      return $this->getLdapUser($id);
    } else {
      return $this->getInternUser($id);
    }

  }

  protected function getInternUser($id){

    $sql = 'select * from user WHERE id=:id';
    $stm = $this->db->prepare($sql);
    $stm->bindParam(':id', $id);
    $result = $stm->execute();
    $return = array();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
      $return[] = $row;
    }
    return $return[0];
  }

  protected function getLdapUser($id){
    $globalConf = new Globalconfig();
    $configuration = $globalConf->getConfig();

    $params = array(
      'hostname' => $configuration['ldapHostname'],
      'base_dn' => $configuration['ldapBaseDn'],
    );

    $ldap = new Manager($params, new Driver());
    $ldap->connect();
    $ldap->bind($configuration['ldapCn'], $configuration['ldapPassword']);

    $results = $ldap->search(Search::SCOPE_ONE, 'uidnumber=' . $id[0]);
    foreach ($results as $node) {
      $mail = $node->get('mail')->getValues();
      $cn = $node->get('cn')->getValues();
      return array('email' => $mail[0],
        'displayName' => $cn[0]);
    }
  }


  public function getAll() {
    $sql = 'select id, email, displayName, active from user';
    $stm = $this->db->prepare($sql);
    $result = $stm->execute();
    $return = array();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
      $return[] = $row;
    }
    return $return;
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

  private function check_password($password, $hash) {
    if ($hash == '') {
      //echo "No password";
      return FALSE;
    }

    if ($hash{0} != '{') {
      if ($password == $hash) {
        return TRUE;
      }
      return FALSE;
    }

    if (substr($hash, 0, 7) == '{crypt}') {
      if (crypt($password, substr($hash, 7)) == substr($hash, 7)) {
        return TRUE;
      }
      return FALSE;
    } elseif (substr($hash, 0, 5) == '{MD5}') {
      $encrypted_password = '{MD5}' . base64_encode(md5($password, TRUE));
    } elseif (substr($hash, 0, 6) == '{SHA1}') {
      $encrypted_password = '{SHA}' . base64_encode(sha1($password, TRUE));
    } elseif (substr($hash, 0, 6) == '{SSHA}') {
      $salt = substr(base64_decode(substr($hash, 6)), 20);
      $encrypted_password = '{SSHA}' . base64_encode(sha1($password . $salt, TRUE) . $salt);
    } else {
      echo "Unsupported password hash format";
      return FALSE;
    }

    if ($hash == $encrypted_password) {
      return TRUE;
    }

    return FALSE;
  }

}