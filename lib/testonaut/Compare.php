<?php
/**
 * Created by PhpStorm.
 * User: maren
 * Date: 06.04.2016
 * Time: 09:40
 */

namespace testonaut;


class Compare {

  public function __construct() {
  }

  public function compareResult($compare, $res, $imageName) {
    if ($compare) {
      $res[] = array(TRUE, 'Compare Image '.$imageName, 'Compare Success');
    } else {
      $res[] = array(FALSE, 'Compare Image '.$imageName, 'Compare Fail');
    }
    return $res;
  }

  public function compare($profile, $imgName, $pagePath, $imageDir) {

    $profileName = $this->getProfileName($profile);


    $path = $imageDir . '/' . $profileName . "/src/" . $imgName;
    $pathref = $imageDir . '/' . $profileName. "/ref/" . $imgName;
    $comp = $imageDir . '/' . $profileName . "/comp/" . $imgName;

    $dir = str_replace('.', '/', $pagePath);
    $web = array (
      Config::getInstance()->appPath.'/web/images/'. $dir. '/' . $profileName . "/src/" . $imgName,
      Config::getInstance()->appPath.'/web/images/'. $dir. '/' . $profileName . "/ref/" . $imgName,
      Config::getInstance()->appPath.'/web/images/'. $dir. '/' . $profileName . "/comp/" . $imgName,
    );

    if (file_exists($pathref)) {
      if (file_exists($comp)) {
        unlink($comp);
      }
      if (class_exists('\\Imagick')) {
        $compare = new Image();

        $result = $compare->compare($path, $pathref, $comp);
      } else {
        $result = FALSE;
      }
    } else {
      $result = FALSE;
    }

    $this->writeCompareToDb($pagePath,$path, $pathref, $comp, $result, $web, $imgName, $profileName);

    return $result;

  }

  protected function getProfileName($profile) {
    if (isset($profile['browser'])) {
      if (isset($profile['name'])) {
        $profileName = $profile['name'] . '_' . $profile['browser'];
      } else {
        $profileName = $profile['browser'] . '_default';
      }
    }
    return $profileName;
  }

  private function writeCompareToDb($path, $src, $pathref, $comp, $result, $web, $imageName, $profile) {
    $db = new \testonaut\Utils\Db(Config::getInstance()->Path . '/index.db');
    $this->db = $db->getInstance();

    $date = new \DateTime();
    $isoDate = $date->format(\DateTime::ISO8601);
    $images = json_encode(array($src, $pathref, $comp));
    $webpath = json_encode($web);

    $sql = "insert into imageCompare (date, path, result, images, webpath, imageName, profile) values (:date, :path, :result, :images, :webpath, :imageName, :profile)";
    $stm = $this->db->prepare($sql);
    $stm->bindParam(':date', $isoDate);
    $stm->bindParam(':path', $path);
    $stm->bindParam(':result', $result);
    $stm->bindParam(':images', $images);
    $stm->bindParam(':webpath', $webpath);
    $stm->bindParam(':imageName', $imageName);
    $stm->bindParam(':profile', $profile);
    $stm->execute();
  }

  public function getComparedImages($path, $children = FALSE) {
    $db = new \testonaut\Utils\Db(Config::getInstance()->Path . '/index.db');
    $this->db = $db->getInstance();

    $extendBinding = '';
    if($children) {
      $sql = "select * from imageCompare where path LIKE :path GROUP BY images ORDER BY date";
      $path = $path.'%';
    } else {
      $sql = "select * from imageCompare where path = :path GROUP BY images ORDER BY date";
    }


    $stm = $this->db->prepare($sql);
    $stm->bindParam(':path', $path);

    $result = $stm->execute();

    $return = array();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)){
      $return[] = array(
        'result' => $row['result'],
        'images' => json_decode($row['images'], true),
        'webpath' => json_decode($row['webpath'], true),
        'imageName' => $row['imageName'],
        'profile' => $row['profile'],
        'path' => $row['path']
      );
    }


    return $return;
  }

}