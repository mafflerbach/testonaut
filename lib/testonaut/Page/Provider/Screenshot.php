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

use mafflerbach\Http\Request;
use mafflerbach\Page\ProviderInterface;
use mafflerbach\Routing;
use testonaut\Compare;
use testonaut\Page;

/**
 * Class Config
 *
 * @package testonaut\Page\Provider
 */
class Screenshot extends Base implements ProviderInterface {

  private $routing;
  protected $path = '';

  public function connect() {
    $this->routing = new Routing();
    $this->response = array(
      'system' => $this->system()
    );


    $this->routing->route('.*/delete/(\w+)/(\w+)/(.*)/(.*)$', function ($src, $browser, $imageName, $path) {

      $this->path = urldecode($path);
      $src = $this->getImagePath() . '/' . $browser . '/' . $src . '/' . $imageName;

      $compare = new Compare();
      $compare->updateComparison($browser, $path, $imageName);

      if (file_exists($src)) {
        if (unlink($src)) {
          $messageBody = 'deleted';
          $result = 'success';

        } else {
          $result = 'fail';
          $messageBody = 'can not delete';
        }
      } else {
        $result = 'fail';
        $messageBody = "can not delete, image doesn't exist";
      }

      $message = array(
        'result' => $result,
        'message' => $messageBody,
        'messageTitle' => 'Delete'
      );

      print(json_encode($message));
      die;
    });
    $this->routing->route('.*/copy/(\w+)/(.*)/(.*)$', function ($browser, $imageName, $path) {

      $this->path = urldecode($path);

      $src = $this->getImagePath() . '/' . $browser . '/src/' . $imageName;
      $ref = $this->getImagePath() . '/' . $browser . '/ref/' . $imageName;

      if (@copy($src, $ref)) {
        $result = 'success';
        $messageBody = 'copied';
      } else {
        $result = 'fail';
        $messageBody = 'can not copy';
      }

      $message = array(
        'result' => $result,
        'message' => $messageBody,
        'messageTitle' => 'Copy'
      );

      print(json_encode($message));
      die;
    });

    $this->routing->route('.*/(.*)$', function ($path) {
      $path = urldecode($path);
      $request = new Request();

      $this->page = new \testonaut\Page($path);
      $this->path = $path;

      $page = new \testonaut\Page($path);
      $compare = new Compare();
      $conf = $page->config();

      if ($conf['type'] == 'project' || $conf['type'] == 'suite') {
        $images = $compare->getComparedImages($path, true);
      } else {
        $images = $compare->getComparedImages($path);
      }
      $images = $this->prepareImageResult($images);

      $root = \testonaut\Config::getInstance()->Path;

      $this->response['menu'] = $this->getMenu($path, 'screenshots');
      $this->response['system']['breadcrumb'] = $this->getBreadcrumb($path);
      $this->response['path'] = $path;
      $this->response['images'] = $images;
      $this->response['imagePath'] = \testonaut\Config::getInstance()->appPath . str_replace($root, '', $page->getImagePath());

      $this->routing->response($this->response);
      $this->routing->render('screenshot.xsl');
    });
  }

  protected function prepareImageResult($images) {
    $me = array();
    for ($i = 0; $i < count($images); $i++) {
      $images[$i]['webpath']['result'] = $images[$i]['result'];
      $images[$i]['webpath']['imageName'] = $images[$i]['imageName'];
      $me[$images[$i]['profile']][$images[$i]['path']]['webpath'][] = $images[$i]['webpath'];
      $me[$images[$i]['profile']][$images[$i]['path']]['images'][] = $images[$i]['images'];
    }

    return $me;
  }

  public function getImagePath() {
    return \testonaut\Config::getInstance()->imageRoot . "/" . $this->relativePath();
  }

  public function relativePath() {
    return str_replace('.', '/', $this->path);
  }

}