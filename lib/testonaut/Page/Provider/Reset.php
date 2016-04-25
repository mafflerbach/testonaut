<?php
/**
 * Created by PhpStorm.
 * User: maren
 * Date: 25.04.2016
 * Time: 21:21
 */

namespace testonaut\Page\Provider;

use Symfony\Component\HttpFoundation\Request;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class Reset implements ControllerProviderInterface {
  public function connect(Application $app) {

    $page = $app['controllers_factory'];
    $page->match('/', function (Request $request) use ($app) {
      return $this->reset($request, $app);
    });
    return $page;
  }


  protected function reset($request, $app) {
    $message = '';
    $name = $request->request->get('email');
    if ($request->isMethod('POST')) {
      $user = new \testonaut\User();
      if ($user->reset($name)) {
        $message = "send E-Mail to: ";
      } else {
        $message = "Can't send E-mail.";
      }
    }

    $app['request'] = array(
      'baseUrl' => $request->getBaseUrl(),
      'mode' => 'reset',
      'content' => '',
      'message' => $message,
    );

    return $app['twig']->render('reset.twig');
  }

}