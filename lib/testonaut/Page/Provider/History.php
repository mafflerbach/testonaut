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
use testonaut\Page;
use testonaut\Page\Breadcrumb;
use testonaut\Settings\Browser;
use testonaut\Utils\Git;

/**
 * Description of History
 *
 * @author maren
 */
class History implements ControllerProviderInterface {

  /**
   * @var $page Page
   */
  private $page;
  private $browsers;
  private $path;

  public function connect(Application $app) {
    $edit = $app['controllers_factory'];
    $edit->get('/', function (Request $request, $path) use ($app) {
      $this->page = new \testonaut\Page($path);
      $this->path = $path;

      if ($request->query->get('delete')) {
        if (isset($_SESSION['testonaut']['userId'])) {
          $this->deleteHistory($request);
        }
      }

      $browserSettings = new Browser($path);
      $this->browsers = $browserSettings->getSettings();

      $app['request'] = array(
        'mode' => 'show',
        'baseUrl' => $request->getBaseUrl(),
        'path' => $path,
        'content' => '',
        'history' => $this->getHistoryList(),
        'githistory' => $this->getGitHistory()

    );
      $crumb = new Breadcrumb($path);
      $app['crumb'] = $crumb->getBreadcrumb();

      return $app['twig']->render('history.twig');
    });

    $edit->post('/compare/{version}/{version2}', function (Request $request, $path, $version, $version2 ) use ($app) {
      return $this->compare($app, $request, $path, $version, $version2);
    });
    $edit->get('/compare/{version}/{version2}', function (Request $request, $path, $version, $version2 ) use ($app) {
      return $this->compare($app, $request, $path, $version, $version2);
    });

    $edit->get('/revert/{version}', function (Request $request, $path, $version) use ($app) {
      var_dump($version);
      var_dump($path);
      return $app['twig']->render('history.twig');
    });

    return $edit;
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
        $limit = ' limit '.$request->query->get('count');
      }
      $sql = 'delete from history '
        . 'where date in ('
        . 'select date from history where browser=:browser and path=:path order by date '.$limit.''
        . ')';
      
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
      $sql = "select * from history where path like :path;";
      $path = $this->path.'%';
    } else {
      $sql = "select * from history where path=:path";
      $path = $this->path;
    }

    $stm = $dbIns->prepare($sql);
    $stm->bindParam(':path', $path);
    $res = $stm->execute();
    
    $foo = array();
    while ($result = $res->fetchArray(SQLITE3_ASSOC)) {
      $date = new \DateTime($result['date']);
      $foo[$result['browser']][$result['path']][$date->format('m.d.Y')][$date->format('H:i:s')]['run'] = json_decode($result['run'], true);
      $foo[$result['browser']][$result['path']][$date->format('m.d.Y')][$date->format('H:i:s')]['result'] = $result['result'];
    }

    return $foo;
  }

}
