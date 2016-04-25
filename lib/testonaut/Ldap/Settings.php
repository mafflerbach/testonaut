<?php

/**
 * Created by PhpStorm.
 * User: maren
 * Date: 25.04.2016
 * Time: 10:22
 */
namespace testonaut\Ldap;

class Settings {

  /*
   *
   *
    $params = array(
      'hostname' => 'localhost',
      'base_dn' => 'dc=example,dc=com',
    );

    $ldap = new Manager($params, new Driver());
    $ldap->connect();
    $ldap->bind('cn=maren', '12qw34er');

   *
   *
   */

  public function __construct() {
  }

  public function getConfig() {
    
  }

  /**
   * @param array $config
   *
   *
    'hostname' => 'localhost',
    'baseDn' => 'dc=example,dc=com'
    'password'
    'cn'
   */
  public function setConfig(array $config) {

  }

}