<?php
/**
 * Created by PhpStorm.
 * User: maren
 * Date: 23.04.2016
 * Time: 22:30
 */

namespace testonaut\Page\Provider;

use Symfony\Component\HttpFoundation\Request;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class Logout implements ControllerProviderInterface {
  public function connect(Application $app) {

    $page = $app['controllers_factory'];
    $page->get('/', function (Request $request) use ($app) {
      unset($_SESSION['testonaut']);

      $app['request'] = array(
        'baseUrl' => $request->getBaseUrl(),
      );

      return $app['twig']->render('logout.twig');
    });
    return $page;
  }
}