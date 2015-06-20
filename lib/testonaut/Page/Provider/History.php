<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
    $sql = "select * from history";
    $stm = $dbIns->prepare($sql);
    $res = $stm->execute();

    $foo = array();
    while ($result = $res->fetchArray(SQLITE3_ASSOC)) {
      $date = new \DateTime($result['date']);
      $foo[$result['browser']][$date->format('m.d.Y')][$date->format('H:i:s')]['run'] = $result['run'];
      $foo[$result['browser']][$date->format('m.d.Y')][$date->format('H:i:s')]['result'] = $result['result'];
    }
    return $foo;
  }

}
