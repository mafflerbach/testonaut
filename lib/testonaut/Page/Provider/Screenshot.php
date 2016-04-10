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

use testonaut\Compare;
use testonaut\Page\Base;
use testonaut\Page\Breadcrumb;
use testonaut\Settings\Browser;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Config
 *
 * @package testonaut\Page\Provider
 */
class Screenshot implements ControllerProviderInterface {
  /**
   * @private
   */
  private $path;

  /**
   * @param Application $app
   * @return mixed
   */
  public function connect(Application $app) {
    $config = $app['controllers_factory'];
    $config->get('/', function (Request $request, $path) use ($app) {

      $page = new \testonaut\Page($path);

      $compare = new Compare();

      $conf = $page->config();

      if ($conf['type'] == 'project' || $conf['type'] == 'suite') {
        $images = $compare->getComparedImages($path, true);
        $images = $this->prepareImageResult($images);
      } else {
        $images = $compare->getComparedImages($path);
        $images = $this->prepareImageResult($images);

      }

      $root = \testonaut\Config::getInstance()->Path;
      $this->path = $path;

      $app['request'] = array(
        'mode' => 'show',
        'baseUrl' => $request->getBaseUrl(),
        'content' => '',
        'images'     => $images,
        'imagePath'  => \testonaut\Config::getInstance()->appPath . str_replace($root, '', $page->getImagePath()),
        'path'       => $path,
        'type'       => 'screenshot',
        );
      $crumb = new Breadcrumb($path);
      $app['crumb'] = $crumb->getBreadcrumb();

      return $app['twig']->render('screenshot.twig');
    });

    return $config;
  }

  protected function prepareImageResult($images) {
    $me = array();
    for($i = 0; $i  < count($images); $i++) {
      $images[$i]['webpath']['result'] = $images[$i]['result'];
      $images[$i]['webpath']['imageName'] = $images[$i]['imageName'];
      $me[$images[$i]['profile']][$images[$i]['path']]['webpath'][] = $images[$i]['webpath'];
      $me[$images[$i]['profile']][$images[$i]['path']]['images'][] = $images[$i]['images'];
    }

    return $me;
  }


}