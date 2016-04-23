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

use testonaut\Config;
use testonaut\Page\Breadcrumb;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use testonaut\Utils\Git;

class Edit implements ControllerProviderInterface {
  protected  $path = '';
  /**
   * @var null| Git
   */
  protected $git = NULL;

  public function connect(Application $app) {
    $edit = $app['controllers_factory'];
    $edit->get('/', function (Request $request, $path) use ($app) {

      if (!isset($_SESSION['testonaut']['userId'])) {
        return $app->redirect($request->getBaseUrl() . '/login/');
      }

      $this->path = $path;
      $page = new \testonaut\Page($path);
      $content = $page->content();
      $uploadedFiles = $page->getLinkedFiles();

      $this->git = new Git($page->transCodePath());

      $app['request'] = array(
        'content'      => $content,
        'path'         => $path,
        'baseUrl'      => $request->getBaseUrl(),
        'linkedFiles'  => $uploadedFiles['documents'],
        'linkedImages' => $uploadedFiles['images'],
        'mode'         => 'edit'
      );

      $crumb = new Breadcrumb($path);
      $app['crumb'] = $crumb->getBreadcrumb();

      return $app['twig']->render('edit.twig');
    })
    ;
    $edit->get('/rename/', function (Request $request, $path) use ($app) {
      $page = new \testonaut\Page($path);
      $app['request'] = array(
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
        'mode'    => 'edit'
      );

      return $app['twig']->render('rename.twig');
    })
    ;
    $edit->post('/rename/', function (Request $request, $path) use ($app) {
      $page = new \testonaut\Page($path);
      $newPath = $request->request->get('newPath');
      $app['request'] = array(
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
        'mode'    => 'edit'
      );
      if ($page->rename($path, $newPath)) {
        $message = "rename Page";

        return $app->redirect($request->getBaseUrl() . '/' . $newPath);
      } else {
        $message = "can't rename Page";
      }
      $app['request'] = array(
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
        'mode'    => 'edit',
        'message' => $message
      );

      return $app['twig']->render('rename.twig');
    })
    ;
    $edit->post('/', function (Request $request, $path) use ($app) {
      $content = $request->request->get('content');
      $page = new \testonaut\Page($path);
      $page->content($content, TRUE);


      if (strpos($path, '.') === FALSE && !$this->git->exists()) {
        $this->git = new Git($page->transCodePath());
        $this->gitInit($page->transCodePath());
      } else {
        $this->git = new Git($page->getProjectRoot());
        $this->gitCommit();
      }

      return $app->redirect($request->getBaseUrl() . '/' . $path);
    })
    ;

    return $edit;
  }

  protected function gitInit($workingDir) {

    $git = new Git($workingDir);
    if (!$git->exists()) {
      $output = $this->git->init();
    }
  }

  protected function gitCommit() {
    $output = $this->git->commit('testcommit', 'maren@afflerbach.info', 'Maren Afflerbach');
    var_dump($output);
    return $output;
  }

}