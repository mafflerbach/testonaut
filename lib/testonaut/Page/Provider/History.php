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
use testonaut\Utils\Git;

class History extends Base implements ProviderInterface {

  private $routing;
  private $response;
  protected $path = '';
  /**
   * @var null| Git
   */
  protected $git = NULL;

  public function connect() {
    $this->routing = new Routing();
    $this->response = array(
      'system' => $this->system()
    );

    $this->routing->route('.*/(.+(?:\..+)*)', function ($path) {
      $path = urldecode($path);
      $request = new Request();

      $this->page = new \testonaut\Page($path);
      $this->path = $path;


      if (!empty($request->post) && empty($_FILES)) {
        $this->handelPostData($path, $request);
      }

      if (!empty($request->post) && !empty($request->files)) {
        $this->handelUpload($path, $request);
      }


      $this->response['menu'] = $this->getMenu($path);
      $this->response['system']['breadcrumb'] = $this->getBreadcrumb($path);

      $this->response['history'] = $this->getHistoryList();


      $this->response['githistory'] = $this->getGitHistory();


      $this->routing->response($this->response);
      $this->routing->render('history.xsl');
    });

    /*
        $edit->post('/compare/{version}/{version2}', function (Request $request, $path, $version, $version2) use ($app) {
          return $this->compare($app, $request, $path, $version, $version2);
        });
        $edit->get('/compare/{version}/{version2}', function (Request $request, $path, $version, $version2) use ($app) {
          return $this->compare($app, $request, $path, $version, $version2);
        });

        $edit->get('/revert/{version}', function (Request $request, $path, $version) use ($app) {

          $user = new User();
          $loadedUser = $user->get($_SESSION['testonaut']['userId']);

          $page = new Page($path);
          $git = new Git($page->getProjectRoot());
          $log = $git->revert($version, $loadedUser['email'], $loadedUser['displayName']);

          $app['request'] = array(
            'mode' => 'revert',
            'baseUrl' => $request->getBaseUrl(),
            'content' => '',
            'path' => $path,
            'message' => $log
          );


          return $app['twig']->render('history.twig');
        });

        return $edit;
    */
  }


  protected function compare($app, $request, $path, $version, $version2) {
    $page = new Page($path);

    $git = new Git($page->getProjectRoot());
    $log = $git->diff($version, $version2, $page->transCodePath());

    $app['request'] = array(
      'mode' => 'compare',
      'baseUrl' => $request->getBaseUrl(),
      'path' => $page->getProjectRootPage(),
      'content' => '',
      'compare' => $log
    );

    return $app['twig']->render('history.twig');
  }


  protected function getGitHistory() {
    $git = new Git($this->page->getProjectRoot());
    return $git->log();
  }

  protected function deleteHistory($request) {
    $db = \testonaut\Config::getInstance()->db;
    $dbIns = $db->getInstance();
    $sql = '';
    if ($request->query->get('all')) {
      $sql = "delete from history where path=:path";
      $stm = $dbIns->prepare($sql);
      $stm->bindParam(':path', $this->path);
    }
    if ($request->query->get('browser')) {
      $browser = $request->query->get('browser');
      $limit = '';
      if ($request->query->get('count')) {
        $limit = ' limit ' . $request->query->get('count');
      }
      $sql = 'delete from history ' . 'where date in (' . 'select date from history where browser=:browser and path=:path order by date ' . $limit . '' . ')';

      $stm = $dbIns->prepare($sql);
      $stm->bindParam(':browser', $browser);
      $stm->bindParam(':path', $this->path);
    }
    $stm->execute();
  }

  protected function getHistoryList() {

    $conf = $this->page->config();

    $db = \testonaut\Config::getInstance()->db;
    $dbIns = $db->getInstance();

    if ($conf['type'] == 'project' || $conf['type'] == 'suite') {
      $sql = "select * from history where path like :path  ORDER by date DESC;";
      $path = $this->path . '%';
    } else {
      $sql = "select * from history where path=:path ORDER by date DESC";
      $path = $this->path;
    }

    $stm = $dbIns->prepare($sql);
    $stm->bindParam(':path', $path);
    $res = $stm->execute();

    $foo = array();
    while ($result = $res->fetchArray(SQLITE3_ASSOC)) {

      $date = new \DateTime($result['date']);


      $foo[$result['browser']][$result['path']][] = array(
        'date' => $date->format('m.d.Y'),
        'time' => $date->format('H:i:s'),
        'run' => json_decode($result['run'], true),
        'result' => $result['result']
      );
    }

    return $foo;
  }

}
