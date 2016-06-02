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
    switch ($compare) {
      case 3;
        $res[] = array(
          TRUE,
          'Reference Image ' . $imageName . 'does not exist',
          ' Comparison impossible'
        );
        break;
      case FALSE;
        $res[] = array(
          FALSE,
          'Compare Image ' . $imageName,
          'Compare Fail'
        );
        break;
      case TRUE;
        $res[] = array(
          TRUE,
          'Compare Image ' . $imageName,
          'Compare Success'
        );
        break;


    }

    return $res;
  }

  public function compare($profile, $imgName, $pagePath, $imageDir) {

    $profileName = $this->getProfileName($profile);


    $path = $imageDir . '/' . $profileName . "/src/" . $imgName;
    $pathref = $imageDir . '/' . $profileName . "/ref/" . $imgName;
    $comp = $imageDir . '/' . $profileName . "/comp/" . $imgName;

    $dir = str_replace('.', '/', $pagePath);
    $web = array(
      Config::getInstance()->appPath . '/web/images/' . $dir . '/' . $profileName . "/src/" . $imgName,
      Config::getInstance()->appPath . '/web/images/' . $dir . '/' . $profileName . "/ref/" . $imgName,
      Config::getInstance()->appPath . '/web/images/' . $dir . '/' . $profileName . "/comp/" . $imgName,
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
      $result = 3;
    }

    $this->writeCompareToDb($pagePath, $path, $pathref, $comp, $result, $web, $imgName, $profileName);

    return $result;
  }

  public function deleteComparison($profile, $path, $imageName) {

    $db = new \testonaut\Utils\Db(Config::getInstance()->Path . '/index.db');
    $this->db = $db->getInstance();

    $sql = "delete from imageCompare where profile = :profile and path = :path and imageName = :imageName";

    $stm = $this->db->prepare($sql);
    $stm->bindParam(':path', $path);
    $stm->bindParam(':profile', $profile);
    $stm->bindParam(':imageName', $imageName);
    $stm->execute();
  }

  public function updateComparison($profile, $path, $imageName) {

    $db = new \testonaut\Utils\Db(Config::getInstance()->Path . '/index.db');
    $this->db = $db->getInstance();

    $sql = "update imageCompare set result = '' where profile = :profile and path = :path and imageName = :imageName";

    $stm = $this->db->prepare($sql);
    $stm->bindParam(':path', $path);
    $stm->bindParam(':profile', $profile);
    $stm->bindParam(':imageName', $imageName);
    $stm->execute();
  }

  protected function getProfileName($profile) {
    if (isset($profile['browser'])) {
      if (isset($profile['name'])) {
        $profileName = $profile['name'] . ' ' . $profile['browser'];
      } else {
        $profileName = $profile['browser'] . ' default';
      }
    }
    return $profileName;
  }

  private function writeCompareToDb($path, $src, $pathref, $comp, $result, $web, $imageName, $profile) {
    $db = new \testonaut\Utils\Db(Config::getInstance()->Path . '/index.db');
    $this->db = $db->getInstance();

    $date = new \DateTime();
    $isoDate = $date->format(\DateTime::ISO8601);
    $images = json_encode(array(
      $src,
      $pathref,
      $comp
    ));
    $webpath = json_encode($web);

    if ($this->exist($imageName, $profile, $path)) {
      $sql = "update imageCompare set 
                date = :date, 
                result = :result 
              where 
                imageName = :imageName AND 
                path = :path AND
                profile = :profile";

      $stm = $this->db->prepare($sql);
      $stm->bindParam(':date', $isoDate);
      $stm->bindParam(':path', $path);
      $stm->bindParam(':result', $result);
      $stm->bindParam(':imageName', $imageName);
      $stm->bindParam(':profile', $profile);

    } else {
      $sql = "insert into imageCompare (date, path, result, images, webpath, imageName, profile) values (:date, :path, :result, :images, :webpath, :imageName, :profile)";

      $stm = $this->db->prepare($sql);
      $stm->bindParam(':date', $isoDate);
      $stm->bindParam(':path', $path);
      $stm->bindParam(':result', $result);
      $stm->bindParam(':images', $images);
      $stm->bindParam(':webpath', $webpath);
      $stm->bindParam(':imageName', $imageName);
      $stm->bindParam(':profile', $profile);

    }

    $stm->execute();
  }


  protected function exist($imageName, $profile, $path) {

    $sql = "select count(*) as count from imageCompare WHERE imageName=:imageName and profile=:profile and path=:path";

    $stm = $this->db->prepare($sql);
    $stm->bindParam(':path', $path);
    $stm->bindParam(':imageName', $imageName);
    $stm->bindParam(':profile', $profile);
    $result = $stm->execute();
    $res = $result->fetchArray(SQLITE3_ASSOC);

    if ($res['count'] > 0) {
      return TRUE;
    }
    return FALSE;
  }

  public function getComparedImages($path, $children = FALSE) {
    $db = new \testonaut\Utils\Db(Config::getInstance()->Path . '/index.db');
    $this->db = $db->getInstance();

    $extendBinding = '';
    if ($children) {
      $sql = "select * from imageCompare where path LIKE :path GROUP BY images ORDER BY date";
      $path = $path . '%';
    } else {
      $sql = "select * from imageCompare where path = :path GROUP BY images ORDER BY date";
    }


    $stm = $this->db->prepare($sql);
    $stm->bindParam(':path', $path);

    $result = $stm->execute();

    $return = array();

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {

      $imageArray = json_decode($row['images'], true);
      $imageWebArray = json_decode($row['webpath'], true);

      $imageAbsolutePath = array();
      $imageRelativPath = array();

      for ($i = 0; $i < count($imageArray); $i++) {
        $type = $this->getImageType($i);
        if (file_exists($imageArray[$i])) {
          $imageAbsolutePath[$type] = $imageArray[$i];
          $imageRelativPath[$type] = $imageWebArray[$i];
        }
      }

      $return[] = array(
        'result' => $row['result'],
        'images' => $imageAbsolutePath,
        'webpath' => $imageRelativPath,
        'imageName' => $row['imageName'],
        'profile' => $row['profile'],
        'path' => $row['path']
      );
    }

    return $return;
  }


  protected function buildCompare() {


  }

  protected function getImageType($i) {
    switch ($i) {
      case 0:
        $type = 'src';
        break;
      case 1:
        $type = 'ref';
        break;
      case 2:
        $type = 'comp';
        break;
    }

    return $type;
  }

}