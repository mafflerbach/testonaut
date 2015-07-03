<?php
/**
 * Created by PhpStorm.
 * User: maren
 * Date: 19.03.2015
 * Time: 19:07
 */

namespace testonaut;

class Image {

  public function compare($image1, $image2, $resultImg) {
      print('asfsd');
    if (class_exists('\\Imagick')) {
      $imageSrc = new \Imagick($image1);
      $imageRef = new \Imagick($image2);

      $result = $imageSrc->compareImages($imageRef, \Imagick::METRIC_MEANSQUAREERROR);
      
      if (isset($result[0])) {
        $result[0]->setImageFormat("png");
        if (file_put_contents($resultImg, $result[0])) {
          return TRUE;
        } else {
          return FALSE;
        }
      } else {
        return FALSE;
      }
    } else {
        var_dump('meee');
      throw new \Exception('class Imagick does not exist');
    }

  }
} 