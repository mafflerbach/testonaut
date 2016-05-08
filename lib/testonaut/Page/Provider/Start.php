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

use mafflerbach\Page\ProviderInterface;
use mafflerbach\Routing;
use testonaut\Generate;


class Start extends Base implements ProviderInterface {

  private $routing;
  private $response;

  public function connect() {
    $this->routing = new Routing();
    $this->response = array(
      'system' => $this->system()
    );

    $this->routing->route('', function () {
      $this->response['content'] = $this->getContent('');

      $this->routing->response($this->response);
      $this->routing->render('page.xsl');
    });

    $this->routing->route('(\w+)', function ($path) {
      $this->response['content'] = $this->getContent($path);

      $this->routing->response($this->response);
      $this->routing->render('page.xsl');
    });

    $this->routing->route('(.+(?:\..+)*)', function ($path) {
      $path = urldecode($path);

      $this->response['content'] = $this->getContent($path);

      $this->routing->response($this->response);
      $this->routing->render('page.xsl');
    });

  }

  private function getContent($path) {
    $page = new \testonaut\Page($path);
    return $page->content();

  }
}


/*
 public function connect(Application $app) {


   $start = $app['controllers_factory'];

   $start->get('/', function (Request $request) use ($app) {
     if (!isset($_SESSION['testonaut']['userId'])) {
       return $app->redirect($request->getBaseUrl() . '/login/');
     }

     $toc = $this->getToc();
     $app['menu'] = $toc;

     $page = new \testonaut\Page('');
     $content = $page->content();

     if ($this->edit) {
       $app['request'] = array(
         'content' => $content,
         'path'    => 'edit',
         'baseUrl' => $request->getBaseUrl(),
         'mode'    => 'edit'
       );
       $crumb = new Breadcrumb('edit');
       $app['crumb'] = $crumb->getBreadcrumb();

       return $app['twig']->render('edit.twig');
     } else {
       $app['request'] = array(
         'content' => $content,
         'path'    => '',
         'baseUrl' => $request->getBaseUrl(),
         'mode'    => 'show',
         'type'    => 'start',
         'update'  => $this->checkVersion()
       );
       $foo = $app['twig']->render('index.twig');

       return new Response($foo, 200, array(
         'Cache-Control' => 'maxage=300',
       ));
     }
   })
   ;
   $start->post('/', function (Request $request) use ($app) {

     $path = $request->request->get('path');
     $content = $request->request->get('content');
     $page = new \testonaut\Page('');
     $page->content($content, TRUE);

     return $app->redirect($request->getBaseUrl() . '/');
   })
   ;

   return $start;
 }

 protected function getToc() {

   $toc = new Generate\Toc(\testonaut\Config::getInstance()->wikiPath);
   $toc->runDir();

   return $toc->generateMenu();
 }

 protected function checkVersion() {

   $versionIni = \testonaut\Config::getInstance()->Path.'/version.ini';

   $iniContent = parse_ini_file($versionIni);
   $version = $iniContent['version'];
   $gitUri = 'https://raw.githubusercontent.com/mafflerbach/testonaut/master/version.ini';
   $rv = parse_ini_string(file_get_contents($gitUri));
   $remoteVersion = $rv['version'];

   if ($remoteVersion > $version) {
     return TRUE;
   }

   return FALSE;
 }

}*/