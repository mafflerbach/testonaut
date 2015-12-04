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

class Image {

  public function compare($image1, $image2, $resultImg) {
    
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
      throw new \Exception('class Imagick does not exist');
    }

  }
} 