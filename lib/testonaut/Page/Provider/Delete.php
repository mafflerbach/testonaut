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

use testonaut\Page\Breadcrumb;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Delete implements ControllerProviderInterface {
  public function connect(Application $app) {
    $edit = $app['controllers_factory'];
    $edit->get('/', function (Request $request, $path) use ($app) {
      $app['request'] = array(
        'path' => $path,
        'baseUrl' => $request->getBaseUrl(),
        'mode' => 'delete',
      );

      $crumb = new Breadcrumb($path);
      $app['crumb'] = $crumb->getBreadcrumb();

      $foo = $app['twig']->render('delete.twig');
      return $foo;
    });

    $edit->post('/', function (Request $request, $path) use ($app) {

      $page = new \testonaut\Page($path);
      $deleted= $page->delete();

      if ($deleted) {
        $message = 'delete';
      } else {
        $message = 'can not delete';
      }

      $app['request'] = array(
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
        'message' => $message,
        'mode'    => 'edit'
      );

      return $app['twig']->render('delete.twig');
    });
    return $edit;
  }
}