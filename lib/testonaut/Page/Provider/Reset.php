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
      $reset = $user->reset($name);
      if ($reset['result']) {
        $message = "Your new password: " . $reset['password'];
      } else {
        $message = "Can't reset password.";
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