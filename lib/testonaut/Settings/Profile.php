<?php

namespace testonaut\Settings;

use testonaut\Config;
use testonaut\Selenium\Api;

class Profile {

  private $profile;
  private $db;
  public function __construct() {
    $db = new \testonaut\Utils\Db(Config::getInstance()->Path . '/index.db');
    $this->db = $db->getInstance();
  }

  public function write($data) {
    $sql = "select * from profile where `name`=:name";
    $stm = $this->db->prepare($sql);

    $stm->bindParam(':name', $data['name']);

    $result = $stm->execute();
    $foo = array();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
      $foo [] = $row;
    }

    if (count($foo) > 0) {
      $this->update($data);
    } else {
      $this->insert($data);
    }
  }

  public function getCustomProfiles() {
    $sql = "select * from profile";
    $stm = $this->db->prepare($sql);
    $result = $stm->execute();

    $foo = array();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
      $foo [] = $row;
    }

    return $foo ;
  }

  public function get() {

    $browserSettings = new Browser("root");
    $browsers = $browserSettings->getSettings();

    $api = new Api();
    $grid = $api->getBrowserList();

    $foo = $this->getCustomProfiles();

    for ($i = 0; $i < count($foo); $i++){
      $browsers[] = $foo[$i];
    }

    $browserProfiles = array('all' => $browsers,
      'grid' => $grid,
      'custom' => $foo
      );
    return $browserProfiles;

  }
  public function getByName($name) {


    $sql = "select * from profile where name = :name";
    $stm = $this->db->prepare($sql);
    $stm->bindParam(':name', $name);
    $result = $stm->execute();

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
      $profile[] = $row;
    }

    return $profile;

  }

  public function delete($name) {

    $sql = "delete from profile where `name` = :name";
    $stm = $this->db->prepare($sql);
    $stm->bindParam(':name', $name);

    $stm->execute();

  }

  protected function update($data) {
    $browser = $data['browser'];
    $name = $data['name'];
    $driverOptions = $data['driverOptions'];
    $arguments = $data['arguments'];
    $capabilities = $data['capabilities'];


    $sql = "update profile set
        `name` = :name,
        browser = :browser,
        driverOptions = :driverOptions,
        arguments = :arguments,
        capabilities = :capabilities
        where `name` = :name
      ";
    $stm = $this->db->prepare($sql);

    $stm->bindParam(':browser', $browser);
    $stm->bindParam(':name', $name);
    $stm->bindParam(':driverOptions', $driverOptions);
    $stm->bindParam(':arguments', $arguments);
    $stm->bindParam(':capabilities', $capabilities);

    $stm->execute();
  }

  protected function insert($data) {
    $browser = $data['browser'];
    $name = $data['name'];
    $driverOptions = $data['driverOptions'];
    $arguments = $data['arguments'];
    $capabilities = $data['capabilities'];

    $sql = "insert into profile (browser, name, driverOptions, arguments, capabilities)
            VALUES (:browser, :name, :driverOptions, :arguments, :capabilities)";
    $stm = $this->db->prepare($sql);

    $stm->bindParam(':browser', $browser);
    $stm->bindParam(':name', $name);
    $stm->bindParam(':driverOptions', $driverOptions);
    $stm->bindParam(':arguments', $arguments);
    $stm->bindParam(':capabilities', $capabilities);

    $stm->execute();
  }




}

?>