<?php

namespace testonaut\Settings;

use testonaut\Config;

class Profile {

  private $profile;
  private $db;
  public function __construct() {
    $db = new \testonaut\Utils\Db(Config::getInstance()->Path . '/index.db');
    $this->db = $db->getInstance();
  }

  public function write($data) {
    $sql = "select * from profile where name = :name";
    $stm = $this->db->prepare($sql);
    $stm->bindParam(':name', $name);

    $result = $stm->execute();
    $result->fetchArray();

    if (count($result) > 0) {
      $this->update($data);
    } else {
      $this->insert($data);
    }
  }

  public function get() {

    $browserSettings = new Browser("root");
    $browsers = $grid = $browserSettings->getSettings();

    $sql = "select * from profile";
    $stm = $this->db->prepare($sql);
    $result = $stm->execute();

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
      $foo [] = $row;
    }

    for ($i = 0; $i < count($foo); $i++){
      $browsers[] = $foo[$i];
    }

    $browserProfiles = array('all' => $browsers,
      'grid' => $grid,
      'custom' => $foo
      );

    return $browserProfiles;

  }

  public function delete($name) {

    $sql = "delete * from profile where name = :name";
    $stm = $this->db->prepare($sql);
    $stm->bindParam(':name', $name);

    $stm->execute();

  }

  protected function update($data) {
    $browser = $data['browser'];
    $name = $data['name'];
    $driverOptions = json_encode($data['driverOption']);
    $arguments = json_encode($data['arguments']);
    $capabilities = json_encode($data['capabilities']);

    $sql = "update profile set
        name = :name
        browser = :browser
        driverOptions = :driverOptions
        arguments = :arguments
        capabilities = :capabilities
        where name = :name
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
    $driverOptions = json_encode($data['driverOption']);
    $arguments = json_encode($data['arguments']);
    $capabilities = json_encode($data['capabilities']);

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