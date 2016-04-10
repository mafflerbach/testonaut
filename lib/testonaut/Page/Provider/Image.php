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


namespace testonaut\Page\Provider;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use testonaut\Compare;

class Image implements ControllerProviderInterface {

  private $path;

  public function connect(Application $app) {

    $image = $app['controllers_factory'];

    $image->get('/copy/{browser}/{image}/{path}', function (Request $request, $browser, $image, $path) use ($app) {

      $this->path = $path;

      $app['request'] = array(
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
        'mode'    => 'edit'
      );

      return $app['twig']->render('copy.twig');
    });

    $image->post('/copy/{browser}/{image}/{path}', function (Request $request, $browser, $image, $path) use ($app) {

      $this->path = $path;

      $src = $this->getImagePath() . '/' . $browser . '/src/' . $image;
      $ref = $this->getImagePath() . '/' . $browser . '/ref/' . $image;

      if (copy($src, $ref)) {
        $message = 'copied';
      } else {
        $message = 'can not copy';
      }

      $app['request'] = array(
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
        'message' => $message,
        'mode'    => 'edit'
      );

      return $app['twig']->render('copy.twig');
    });

    $image->get('/delete/{type}/{browser}/{image}/{path}', function (Request $request, $type, $browser, $image, $path) use ($app) {

      $this->path = $path;

      $app['request'] = array(
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
        'mode'    => 'edit'
      );

      return $app['twig']->render('deleteImage.twig');
    });

    $image->post('/delete/{type}/{browser}/{image}/{path}', function (Request $request, $type, $browser, $image, $path) use ($app) {

      $this->path = $path;
      $src = $this->getImagePath() . '/' . $browser . '/' . $type . '/' . $image;

      $compare = new Compare();
      $compare->deleteComparison($browser, $path, $image);
      
      if (file_exists($src)) {
        if (unlink($src)) {
          $message = 'delete';
        } else {
          $message = 'can not delete';
        }
      } else {
        $message = "can not delete, image doesn't exist";

      }

      $app['request'] = array(
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
        'message' => $message,
        'mode'    => 'edit'
      );

      return $app['twig']->render('deleteImage.twig');
    });

    return $image;
  }

  public function getImagePath() {

    return \testonaut\Config::getInstance()->imageRoot . "/" . $this->relativePath();
  }

  public function relativePath() {

    return str_replace('.', '/', $this->path);
  }
}