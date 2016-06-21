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

  /**
   * @param $data
   */
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

  /**
   * @return array
   */
  public function getCustomProfiles() {
    $sql = "select * from profile";
    $stm = $this->db->prepare($sql);
    $result = $stm->execute();

    $foo = array();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
      $foo[] = array(
        'browser' => $row['browser'],
        'name' => $row['name'],
        'driverOptions' => json_decode($row['driverOptions'], true),
        'arguments' => json_decode($row['arguments'], true),
        'capabilities' => json_decode($row['capabilities'], true),
        'os' => $row['os'],
        'version' => $row['version'],
        'local' => $row['local'],
      );

    }
    return $foo;
  }

  /**
   * @return array
   */
  public function get() {

    $browserSettings = new Browser("root");
    $browsers = $browserSettings->getSettings();

    $api = new Api();
    $grid = $api->getBrowserList();
    $foo = $this->getCustomProfiles();
    $saucelabs = $this->getActiveSaucelabsBrowsers();

    $browserProfiles = array(
      'all' => $browsers,
      'grid' => $grid,
      'custom' => $foo,
      'saucelabs' => $saucelabs
    );
    return $browserProfiles;

  }


  private function getActiveSaucelabsBrowsers() {

    $file = \testonaut\Config::getInstance()->Path . '/saucelabsInstances.json';
    if (file_exists($file)) {
      $platforms = json_decode(file_get_contents($file), true);
      return $platforms;
    }
  }


  private function getSaucelabsBrowsers() {

    $file = \testonaut\Config::getInstance()->Path . '/saucelabsPlatforms.json';
    $oses = array();
    if (file_exists($file)) {
      $platforms = json_decode(file_get_contents($file), true);

      for($i = 0; $i < count($platforms); $i++) {
        if (isset($platforms[$i]['device'])) {
          continue;
        }
        if (!@in_array($platforms[$i]['api_name'], $oses[$platforms[$i]['os']]['browser'])) {
          $oses[$platforms[$i]['os']][$platforms[$i]['api_name']][] = $platforms[$i]['short_version'];
        }
      }
    }

    return $oses;
  }

  /**
   * @param $name
   * @return array
   */
  public function getByName($name) {
    $name = urldecode($name);

    $sql = "select * from profile where name = :name";
    $stm = $this->db->prepare($sql);
    $stm->bindParam(':name', $name);
    $result = $stm->execute();

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
      $profile[] = $row;
    }


    return $profile;
  }

  /**
   * @param $name
   */
  public function delete($name) {
    $name = urldecode($name);

    $sql = "delete from profile where `name` = :name";
    $stm = $this->db->prepare($sql);
    $stm->bindParam(':name', $name);

    return $stm->execute();

  }

  /**
   * @param $data
   */
  protected function update($data) {

    $browser = $data['browser'];
    $name = $data['name'];
    $driverOptions = $data['driverOptions'];
    $arguments = $data['arguments'];
    $capabilities = $data['capabilities'];
    $os = $data['os'];
    $version = $data['version'];
    $local = ($data['local']) ? 1 : 0;

    $sql = "update profile set
        `name` = :name,
        browser = :browser,
        driverOptions = :driverOptions,
        arguments = :arguments,
        capabilities = :capabilities,
        os = :os,
        version = :version,
        local = :local
        where `name` = :name
      ";
    $stm = $this->db->prepare($sql);

    $stm->bindParam(':browser', $browser);
    $stm->bindParam(':name', $name);
    $stm->bindParam(':driverOptions', $driverOptions);
    $stm->bindParam(':arguments', $arguments);
    $stm->bindParam(':capabilities', $capabilities);
    $stm->bindParam(':os', $os);
    $stm->bindParam(':version', $version);
    $stm->bindParam(':local', $local);

    $stm->execute();
  }

  /**
   * @param $data
   */
  protected function insert($data) {

    $browser = $data['browser'];
    $name = $data['name'];
    $driverOptions = $data['driverOptions'];
    $arguments = $data['arguments'];
    $capabilities = $data['capabilities'];
    $os = $data['os'];
    $version = $data['version'];
    $local = ($data['local']) ? 1 : 0;


    $sql = "insert into profile (browser, name, driverOptions, arguments, capabilities, os, version, local)
            VALUES (:browser, :name, :driverOptions, :arguments, :capabilities, :os, :version, :local)";
    $stm = $this->db->prepare($sql);

    $stm->bindParam(':browser', $browser);
    $stm->bindParam(':name', $name);
    $stm->bindParam(':driverOptions', $driverOptions);
    $stm->bindParam(':arguments', $arguments);
    $stm->bindParam(':capabilities', $capabilities);
    $stm->bindParam(':os', $os);
    $stm->bindParam(':version', $version);
    $stm->bindParam(':local', $local);


    $stm->execute();
  }

}

?>