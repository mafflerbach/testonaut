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
use testonaut\Page\Breadcrumb;
use testonaut\Settings\Browser;

/**
 * Description of History
 *
 * @author maren
 */
class History implements ControllerProviderInterface {

  private $page;
  private $browsers;
  private $path;

  public function connect(Application $app) {
    $edit = $app['controllers_factory'];
    $edit->get('/', function (Request $request, $path) use ($app) {
      $this->page = new \testonaut\Page($path);
      $this->path = $path;
      
      if ($request->query->get('delete')) {
        $this->deleteHistory($request);
      }

      $browserSettings = new Browser($path);
      $this->browsers = $browserSettings->getSettings();

      $app['request'] = array(
        'mode' => 'show',
        'baseUrl' => $request->getBaseUrl(),
        'content' => '',
        'history' => $this->getHistoryList()
      );
      $crumb = new Breadcrumb($path);
      $app['crumb'] = $crumb->getBreadcrumb();

      return $app['twig']->render('history.twig');
    });

    return $edit;
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

    $db = \testonaut\Config::getInstance()->db;
    $dbIns = $db->getInstance();
    $sql = "select * from history where path=:path";
    $stm = $dbIns->prepare($sql);
    $stm->bindParam(':path', $this->path);
    $res = $stm->execute();
    
    $foo = array();
    while ($result = $res->fetchArray(SQLITE3_ASSOC)) {
      $date = new \DateTime($result['date']);
      $foo[$result['browser']][$date->format('m.d.Y')][$date->format('H:i:s')]['run'] = json_decode($result['run'], true);
      $foo[$result['browser']][$date->format('m.d.Y')][$date->format('H:i:s')]['result'] = $result['result'];
    }
    return $foo;
  }

}
